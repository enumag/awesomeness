<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient\Internal;

use Amp\Deferred;
use Amp\Promise;
use Prooph\EventStoreClient\ConnectionSettings;
use Prooph\EventStoreClient\Internal\Message\StartPersistentSubscriptionMessage;
use Prooph\EventStoreClient\UserCredentials;
use Psr\Log\LoggerInterface as Logger;

class EventStorePersistentSubscription extends AbstractEventStorePersistentSubscription
{
    /** @var EventStoreConnectionLogicHandler */
    private $handler;

    /** @internal  */
    public function __construct(
        string $subscriptionId,
        string $streamId,
        callable $eventAppeared,
        ?callable $subscriptionDropped,
        UserCredentials $userCredentials,
        Logger $logger,
        bool $verboseLogging,
        ConnectionSettings $settings,
        EventStoreConnectionLogicHandler $handler,
        int $bufferSize = 10,
        bool $autoAck = true
    ) {
        parent::__construct(
            $subscriptionId,
            $streamId,
            $eventAppeared,
            $subscriptionDropped,
            $userCredentials,
            $logger,
            $verboseLogging,
            $settings,
            $bufferSize, $autoAck
        );

        $this->handler = $handler;
    }

    public function startSubscription(
        string $subscriptionId,
        string $streamId,
        int $bufferSize,
        UserCredentials $userCredentials,
        callable $onEventAppeared,
        ?callable $onSubscriptionDropped,
        ConnectionSettings $settings
    ): Promise {
        $deferred = new Deferred();

        $this->handler->enqueueMessage(new StartPersistentSubscriptionMessage(
            $deferred,
            $subscriptionId,
            $streamId,
            $bufferSize,
            $userCredentials,
            $onEventAppeared,
            $onSubscriptionDropped,
            $settings->maxRetries(),
            $settings->operationTimeout()
        ));

        return $deferred->promise();
    }
}
