<?php

declare(strict_types=1);

namespace Prooph\PostgresProjectionManager;

use Amp\Coroutine;
use Amp\Failure;
use Amp\Loop;
use Amp\Postgres\Connection;
use Amp\Postgres\Pool as PostgresPool;
use Amp\Postgres\ResultSet;
use Amp\Postgres\Statement;
use Amp\Promise;
use Amp\Success;
use Error;
use Generator;
use Prooph\EventStore\Common\SystemStreams;
use Prooph\EventStore\Exception\ProjectionNotFound;
use Prooph\EventStore\Exception\RuntimeException;
use Prooph\EventStore\SystemSettings;
use Psr\Log\LoggerInterface as PsrLogger;
use Throwable;
use function assert;

class ProjectionManager
{
    private const STOPPED = 0;
    private const STARTING = 1;
    private const STARTED = 2;
    private const STOPPING = 3;

    private const STATES = [
        self::STOPPED => 'STOPPED',
        self::STARTING => 'STARTING',
        self::STARTED => 'STARTED',
        self::STOPPING => 'STOPPING',
    ];

    private const DEFAULT_SHUTDOWN_TIMEOUT = 3000;

    /** @var int */
    private $state = self::STOPPED;
    /** @var PostgresPool */
    private $postgresPool;
    /** @var PsrLogger */
    private $logger;
    /** @var Projector[] */
    private $projectors = [];
    /** @var SystemSettings */
    private $settings;
    /** @var Connection */
    private $lockConnection;

    public function __construct(PostgresPool $pool, PsrLogger $logger)
    {
        $this->postgresPool = $pool;
        $this->logger = $logger;
    }

    public function start(): Promise
    {
        try {
            if ($this->state === self::STOPPED) {
                return new Coroutine($this->doStart());
            }

            return new Failure(new Error(
                'Cannot start server: already '.self::STATES[$this->state]
            ));
        } catch (Throwable $uncaught) {
            return new Failure($uncaught);
        }
    }

    private function doStart(): Generator
    {
        assert($this->logger->debug('Starting') || true);

        $this->state = self::STARTING;

        try {
            $this->lockConnection = yield $this->postgresPool->extractConnection();
            /** @var Statement $statement */
            $statement = yield $this->lockConnection->prepare('SELECT PG_TRY_ADVISORY_LOCK(HASHTEXT(:name)) as stream_lock;');
            /** @var ResultSet $result */
            $result = yield $statement->execute(['name' => 'projection-manager']);
            yield $result->advance(ResultSet::FETCH_OBJECT);
            $lock = $result->getCurrent()->stream_lock;
            $this->logger->debug(var_export($lock, true));

            if (! $lock) {
                throw new RuntimeException(
                    'Could not acquire lock for projection manager, another process already running?'
                );
            }

            yield new Coroutine($this->loadSystemSettings());

            /** @var ResultSet $result */
            $result = yield $this->postgresPool->execute('SELECT projection_id, projection_name from projections;');

            while (yield $result->advance(ResultSet::FETCH_OBJECT)) {
                $projectionName = $result->getCurrent()->projection_name;
                $projectionId = $result->getCurrent()->projection_id;

                $projector = new Projector($this->postgresPool, $projectionName, $projectionId, $this->logger, $this->settings);
                $this->projectors[$projectionName] = $projector;

                yield $projector->tryStart();
            }

            $this->state = self::STARTED;
            assert($this->logger->debug('Started') || true);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
            Loop::stop();
            yield new Failure($e);
        }
    }

    /** @throws Throwable */
    private function loadSystemSettings(): Generator
    {
        /** @var Statement $statement */
        $statement = yield $this->postgresPool->prepare(<<<SQL
SELECT * FROM streams WHERE stream_name = ?
SQL
        );
        /** @var ResultSet $result */
        $result = yield $statement->execute([SystemStreams::SettingsStream]);

        if (! yield $result->advance(ResultSet::FETCH_OBJECT)) {
            $this->settings = SystemSettings::default();

            return null;
        }

        $streamData = $result->getCurrent();

        /** @var Statement $statement */
        $statement = yield $this->postgresPool->prepare(<<<SQL
SELECT
    COALESCE(e1.event_id, e2.event_id) as event_id,
    e1.event_number as event_number,
    COALESCE(e1.event_type, e2.event_type) as event_type,
    COALESCE(e1.data, e2.data) as data,
    COALESCE(e1.meta_data, e2.meta_data) as meta_data,
    COALESCE(e1.is_json, e2.is_json) as is_json,
    COALESCE(e1.updated, e2.updated) as updated
FROM
    events e1
LEFT JOIN events e2
    ON (e1.link_to = e2.event_id)
WHERE e1.stream_id = ?
ORDER BY e1.event_number DESC
LIMIT 1
SQL
        );
        /** @var ResultSet $result */
        $result = yield $statement->execute([$streamData->stream_id]);

        if (! yield $result->advance(ResultSet::FETCH_OBJECT)) {
            $this->settings = SystemSettings::default();

            return null;
        }

        $event = $result->getCurrent();

        $data = json_decode($event->data, true);

        if (0 !== json_last_error()) {
            throw new Error('Could not json decode system settings');
        }

        $this->settings = SystemSettings::fromArray($data);

        return null;
    }

    /**
     * Stop the server.
     *
     * @param int $timeout Number of milliseconds to allow clients to gracefully shutdown before forcefully closing.
     *
     * @return Promise
     */
    public function stop(int $timeout = self::DEFAULT_SHUTDOWN_TIMEOUT): Promise
    {
        switch ($this->state) {
            case self::STARTED:
                return new Coroutine($this->doStop($timeout));
            case self::STOPPED:
                return new Success();
            default:
                return new Failure(new \Error(
                    'Cannot stop projection manager: currently '.self::STATES[$this->state]
                ));
        }
    }

    public function stopProjection(string $name): Promise
    {
        if (! isset($this->projectors[$name])) {
            return new Failure(ProjectionNotFound::withName($name));
        }

        return $this->projectors[$name]->stop();
    }

    private function doStop(int $timeout): Generator
    {
        assert($this->logger->debug('Stopping') || true);
        $this->state = self::STOPPING;

        foreach ($this->projectors as $projector) {
            yield $projector->stop();
        }

        yield new Success();

        assert($this->logger->debug('Stopped') || true);
        $this->state = self::STOPPED;
    }
}