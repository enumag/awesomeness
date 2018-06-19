<?php

declare(strict_types=1);

namespace Prooph\PostgresProjectionManager\Operations;

use Amp\Coroutine;
use Amp\Postgres\Pool;
use Amp\Postgres\Statement;
use Amp\Postgres\Transaction;
use DateTimeImmutable;
use DateTimeZone;
use Generator;
use Prooph\EventStore\EventId;
use Prooph\EventStore\Exception\ProjectionException;
use Prooph\EventStore\Internal\DateTimeUtil;
use Prooph\EventStore\Internal\Principal;
use Prooph\EventStore\ProjectionManagement\CreateProjectionResult;
use Prooph\EventStore\Projections\ProjectionEventTypes;
use Prooph\EventStore\Projections\ProjectionNames;
use Prooph\EventStore\Projections\StandardProjections;
use Ramsey\Uuid\Uuid;
use function Amp\Promise\all;

/** @internal */
class CreateProjectionOperation
{
    /** @var Pool */
    private $pool;
    /** @var GetExpectedVersionOperation */
    private $getExpectedVersionOperation;
    /** @var LockOperation */
    private $lockOperation;
    /** @var string[] */
    private $locks = [];

    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
        $this->getExpectedVersionOperation = new GetExpectedVersionOperation($pool);
        $this->lockOperation = new LockOperation($pool);
    }

    public function __invoke(
        string $mode,
        string $name,
        string $type,
        string $query,
        bool $enabled,
        ?bool $checkpoints,
        ?bool $emit,
        ?bool $trackEmittedStreams,
        Principal $runAs
    ): Generator {
        if ($type !== 'PHP') {
            throw new ProjectionException('Only projection type support for now is \'PHP\'');
        }

        if (StandardProjections::isStandardProjection($name)) {
            throw new ProjectionException('Cannot override standard projections');
        }

        $projectionId = \str_replace('-', '', Uuid::uuid4()->toString());

        yield from $this->lockMulti(
            'projection-' . $name,
            ProjectionNames::ProjectionsRegistrationStream,
            ProjectionNames::ProjectionsMasterStream,
            ProjectionNames::ProjectionsStreamPrefix . $name
        );

        $getExpectedVersionOperation = $this->getExpectedVersionOperation;

        /** @var Transaction $transaction */
        $transaction = yield $this->pool->transaction();

        /** @var Statement $projectionStatement */
        $projectionStatement = yield $transaction->prepare(
            'INSERT INTO projections (projection_name, projection_id) VALUES (?, ?);'
        );

        $sql = <<<SQL
INSERT INTO events (event_id, event_number, event_type, data, meta_data, stream_name, is_json, updated) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?), (?, ?, ?, ?, ?, ?, ?, ?), (?, ?, ?, ?, ?, ?, ?, ?);
SQL;

        /** @var Statement $statement */
        $statement = yield $transaction->prepare($sql);

        if ($mode === 'Transient') {
            $eventData = [
                'handlerType' => 'PHP',
                'query' => $query,
                'mode' => $mode,
                'enabled' => $enabled,
                'runAs' => $runAs->toArray(),
                'checkpointHandledThreshold' => 4000,
                'checkpointUnhandledBytesThreshold' => 10000000,
                'pendingEventsThreshold' => 5000,
                'maxWriteBatchLength' => 500,
            ];
        } else {
            $eventData = [
                'handlerType' => 'PHP',
                'query' => $query,
                'mode' => $mode,
                'enabled' => $enabled,
                'emitEnabled' => $emit,
                'checkpointsDisabled' => ! $checkpoints,
                'trackEmittedStreams' => $trackEmittedStreams,
                'runAs' => $runAs->toArray(),
                'checkpointHandledThreshold' => 4000,
                'checkpointUnhandledBytesThreshold' => 10000000,
                'pendingEventsThreshold' => 5000,
                'maxWriteBatchLength' => 500,
            ];
        }

        $now = DateTimeUtil::format(new DateTimeImmutable('NOW', new DateTimeZone('UTC')));

        $params = [
            // registration stream
            EventId::generate()->toString(),
            yield from $getExpectedVersionOperation(ProjectionNames::ProjectionsRegistrationStream),
            ProjectionEventTypes::ProjectionCreated,
            $name,
            '',
            ProjectionNames::ProjectionsRegistrationStream,
            false,
            $now,
            // master stream
            EventId::generate()->toString(),
            yield from $getExpectedVersionOperation(ProjectionNames::ProjectionsMasterStream),
            '$prepared',
            \json_encode([
                'id' => $projectionId,
            ]),
            '',
            ProjectionNames::ProjectionsMasterStream,
            true,
            $now,
            // projection stream
            EventId::generate()->toString(),
            yield from $getExpectedVersionOperation(ProjectionNames::ProjectionsStreamPrefix . $name),
            ProjectionEventTypes::ProjectionUpdated,
            \json_encode($eventData),
            '',
            ProjectionNames::ProjectionsStreamPrefix . $name,
            true,
            $now,
        ];

        try {
            yield $projectionStatement->execute([
                $name,
                $projectionId,
            ]);
            yield $statement->execute($params);
            yield $transaction->commit();
        } catch (\Throwable $e) {
            return CreateProjectionResult::conflict();
        } finally {
            yield from $this->releaseAll();
        }

        return CreateProjectionResult::success();
    }

    private function lockMulti(array $names): Generator
    {
        $promises = [];

        foreach ($names as $name) {
            $promises[] = new Coroutine($this->lockOperation->acquire($name));
            $this->locks[] = $name;
        }

        yield all($promises);
    }

    private function releaseAll(): Generator
    {
        $promises = [];

        foreach ($this->locks as $lock) {
            $promises = new Coroutine($this->lockOperation->release($lock));
        }

        $this->locks = [];

        yield all($promises);
    }
}
