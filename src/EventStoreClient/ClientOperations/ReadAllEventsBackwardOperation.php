<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient\ClientOperations;

use Amp\Deferred;
use Google\Protobuf\Internal\Message;
use Prooph\EventStoreClient\Data\AllEventsSlice;
use Prooph\EventStoreClient\Data\Position;
use Prooph\EventStoreClient\Data\ReadDirection;
use Prooph\EventStoreClient\Data\ResolvedEvent;
use Prooph\EventStoreClient\Data\UserCredentials;
use Prooph\EventStoreClient\Exception\AccessDeniedException;
use Prooph\EventStoreClient\Exception\ServerError;
use Prooph\EventStoreClient\Internal\EventMessageConverter;
use Prooph\EventStoreClient\Internal\SystemData\InspectionDecision;
use Prooph\EventStoreClient\Internal\SystemData\InspectionResult;
use Prooph\EventStoreClient\Messages\ClientMessages\ReadAllEvents;
use Prooph\EventStoreClient\Messages\ClientMessages\ReadAllEventsCompleted;
use Prooph\EventStoreClient\Messages\ClientMessages\ReadAllEventsCompleted\ReadAllResult;
use Prooph\EventStoreClient\Messages\ClientMessages\ResolvedIndexedEvent;
use Prooph\EventStoreClient\Transport\Tcp\TcpCommand;
use Psr\Log\LoggerInterface as Logger;

/** @internal */
class ReadAllEventsBackwardOperation extends AbstractOperation
{
    /** @var bool */
    private $requireMaster;
    /** @var Position */
    private $position;
    /** @var int */
    private $maxCount;
    /** @var bool */
    private $resolveLinkTos;

    public function __construct(
        Logger $logger,
        Deferred $deferred,
        bool $requireMaster,
        Position $position,
        int $maxCount,
        bool $resolveLinkTos,
        ?UserCredentials $userCredentials
    ) {
        $this->requireMaster = $requireMaster;
        $this->position = $position;
        $this->maxCount = $maxCount;
        $this->resolveLinkTos = $resolveLinkTos;

        parent::__construct(
            $logger,
            $deferred,
            $userCredentials,
            TcpCommand::readAllEventsBackward(),
            TcpCommand::readAllEventsBackwardCompleted(),
            ReadAllEventsCompleted::class
        );
    }

    protected function createRequestDto(): Message
    {
        $message = new ReadAllEvents();
        $message->setRequireMaster($this->requireMaster);
        $message->setCommitPosition($this->position->commitPosition());
        $message->setPreparePosition($this->position->preparePosition());
        $message->setMaxCount($this->maxCount);
        $message->setResolveLinkTos($this->resolveLinkTos);

        return $message;
    }

    protected function inspectResponse(Message $response): InspectionResult
    {
        /** @var ReadAllEventsCompleted $response */
        switch ($response->getResult()) {
            case ReadAllResult::Success:
                $this->succeed($response);

                return new InspectionResult(InspectionDecision::endOperation(), 'Success');
            case ReadAllResult::Error:
                $this->fail(new ServerError($response->getError()));

                return new InspectionResult(InspectionDecision::endOperation(), 'Error');
            case
            ReadAllResult::AccessDenied:
                $this->fail(AccessDeniedException::toAllStream());

                return new InspectionResult(InspectionDecision::endOperation(), 'AccessDenied');
            default:
                throw new ServerError('Unexpected ReadAllResult');
        }
    }

    protected function transformResponse(Message $response)
    {
        /* @var ReadAllEventsCompleted $response */
        $records = $response->getEvents();

        $resolvedEvents = [];

        foreach ($records as $record) {
            /** @var ResolvedIndexedEvent $record */
            $event = EventMessageConverter::convertEventRecordMessageToEventRecord($record->getEvent());
            $link = null;

            if ($link = $record->getLink()) {
                $link = EventMessageConverter::convertEventRecordMessageToEventRecord($link);
            }

            $resolvedEvents[] = new ResolvedEvent($event, $link, null);
        }

        return new AllEventsSlice(
            ReadDirection::backward(),
            new Position($response->getCommitPosition(), $response->getPreparePosition()),
            new Position($response->getNextCommitPosition(), $response->getNextPreparePosition()),
            $resolvedEvents
        );
    }
}