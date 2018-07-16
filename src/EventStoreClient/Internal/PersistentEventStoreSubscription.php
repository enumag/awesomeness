<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient\Internal;

use Prooph\EventStore\Data\EventId;
use Prooph\EventStore\Data\PersistentSubscriptionNakEventAction;

/** @internal */
class PersistentEventStoreSubscription extends EventStoreSubscription
{
    /** @var ConnectToPersistentSubscriptions */
    private $subscriptionOperation;

    public function __construct(
        ConnectToPersistentSubscriptions $subscriptionOperation,
        string $streamId,
        int $lastCommitPosition,
        ?int $lastEventNumber
    ) {
        parent::__construct(
            $this->streamId(),
            $this->lastCommitPosition(),
            $this->lastEventNumber()
        );

        $this->subscriptionOperation = $subscriptionOperation;
    }

    public function unsubscribe(): void
    {
        $this->subscriptionOperation->unsubscribe();
    }

    /** @param EventId[] $eventIds */
    public function notifyEventsProcessed(array $eventIds): void
    {
        $this->subscriptionOperation->notifyEventsProcessed($eventIds);
    }

    /**
     * @param EventId[] $eventIds
     * @param PersistentSubscriptionNakEventAction $action
     * @param string $reason
     */
    public function notifyEventsFailed(
        array $eventIds,
        PersistentSubscriptionNakEventAction $action,
        string $reason
    ): void {
        $this->subscriptionOperation->notifyEventsFailed($eventIds, $action, $reason);
    }
}