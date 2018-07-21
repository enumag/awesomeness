<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient\Internal;

use Amp\Delayed;
use Amp\Loop;
use Amp\Promise;
use Amp\Success;
use Generator;
use Prooph\EventStoreClient\ConnectionSettings;
use Prooph\EventStoreClient\Data\EventId;
use Prooph\EventStoreClient\Data\PersistentSubscriptionNakEventAction;
use Prooph\EventStoreClient\Data\PersistentSubscriptionResolvedEvent;
use Prooph\EventStoreClient\Data\ResolvedEvent;
use Prooph\EventStoreClient\Data\SubscriptionDropReason;
use Prooph\EventStoreClient\Data\UserCredentials;
use Prooph\EventStoreClient\Exception\TimeoutException;
use SplQueue;
use Throwable;
use function Amp\call;

/** @internal  */
abstract class AbstractEventStorePersistentSubscription
{
    /** @var ResolvedEvent */
    private static $dropSubscriptionEvent;

    public const DefaultBufferSize = 10;

    /** @var string */
    private $subscriptionId;
    /** @var string */
    private $streamId;
    /** @var callable(self $subscription, ResolvedEvent $event, ?int $retryCount): Promise */
    private $eventAppeared;
    /** @var null|callable(self $subscription, SubscriptionDropReason $reason, Throwable $exception):void */
    private $subscriptionDropped;
    /** @var UserCredentials */
    private $userCredentials;
    //private readonly ILogger _log;
    //private bool $verbose;
    /** @var ConnectionSettings */
    private $settings;
    /** @var bool */
    private $autoAck;

    /** @var PersistentEventStoreSubscription */
    private $subscription;
    /** @var SplQueue */
    private $queue;
    /** @var int */
    private $isProcessing = false;
    /** @var DropData */
    private $dropData;

    /** @var int */
    private $isDropped;
    //private readonly ManualResetEventSlim _stopped = new ManualResetEventSlim(true);
    /** @var int */
    private $bufferSize;
    /** @var bool */
    private $stopped = true;

    /**
     * @internal
     *
     * @param string $subscriptionId
     * @param string $streamId
     * @param callable(self $subscription, ResolvedEvent $event, ?int $retryCount): Promise $eventAppeared
     * @param null|callable(self $subscription, SubscriptionDropReason $reason, Throwable $exception): void $subscriptionDropped
     * @param UserCredentials $userCredentials
     * @param ConnectionSettings $settings
     * @param int $bufferSize
     * @param bool $autoAck
     */
    public function __construct(
        string $subscriptionId,
        string $streamId,
        callable $eventAppeared,
        ?callable $subscriptionDropped,
        UserCredentials $userCredentials,
        //ILogger log,
        //bool verboseLogging,
        ConnectionSettings $settings,
        int $bufferSize = 10,
        bool $autoAck = true
    ) {
        if (null === self::$dropSubscriptionEvent) {
            self::$dropSubscriptionEvent = new ResolvedEvent(null, null, null);
        }

        $this->subscriptionId = $subscriptionId;
        $this->streamId = $streamId;
        $this->eventAppeared = $eventAppeared;
        $this->subscriptionDropped = $subscriptionDropped ?? function (): void {
        };
        $this->userCredentials = $userCredentials;
        //$this->log = $log;
        //$this->verbose = $verboseLogging;
        $this->settings = $settings;
        $this->bufferSize = $bufferSize;
        $this->autoAck = $autoAck;
        $this->queue = new SplQueue();
    }

    /**
     * @internal
     *
     * @return Promise<self>
     */
    public function start(): Promise
    {
        $this->stopped = false;

        $onEventAppeared = function (
            EventStoreSubscription $subscription,
            PersistentSubscriptionResolvedEvent $resolvedEvent
        ): Promise {
            return $this->onEventAppeared($subscription, $resolvedEvent);
        };

        $onSubscriptionDropped = function (
            EventStoreSubscription $subscription,
            SubscriptionDropReason $reason,
            Throwable $exception
        ): void {
            $this->onSubscriptionDropped($subscription, $reason, $exception);
        };

        $promise = $this->startSubscription(
            $this->subscriptionId,
            $this->streamId,
            $this->bufferSize,
            $this->userCredentials,
            $onEventAppeared,
            $onSubscriptionDropped,
            $this->settings
        );

        $promise->onResolve(function (?Throwable $exeption, &$result) {
            $this->subscription = $result;
            $result = $this;
        });

        return $promise;
    }

    /**
     * @internal
     *
     * @param string $subscriptionId
     * @param string $streamId
     * @param int $bufferSize
     * @param UserCredentials $userCredentials
     * @param callable(EventStoreSubscription $subscription, PersistentSubscriptionResolvedEvent $resolvedEvent): Promise $onEventAppeared,
     * @param null|callable(EventStoreSubscription $subscription, SubscriptionDropReason $reason, Throwable $exception): void $onSubscriptionDropped
     * @param ConnectionSettings $settings
     * @return Promise
     */
    abstract public function startSubscription(
        string $subscriptionId,
        string $streamId,
        int $bufferSize,
        UserCredentials $userCredentials,
        callable $onEventAppeared,
        ?callable $onSubscriptionDropped,
        ConnectionSettings $settings
    ): Promise;

    /**
     * Acknowledge that a message have completed processing (this will tell the server it has been processed)
     * Note: There is no need to ack a message if you have Auto Ack enabled
     *
     * @param ResolvedEvent $event
     *
     * @return void
     */
    public function acknowledge(ResolvedEvent $event): void
    {
        $this->subscription->notifyEventsProcessed([$event->originalEvent()->eventId()]);
    }

    /**
     * Acknowledge that a message have completed processing (this will tell the server it has been processed)
     * Note: There is no need to ack a message if you have Auto Ack enabled
     *
     * @param ResolvedEvent[] $event
     *
     * @return void
     */
    public function acknowledgeMultiple(array $events): void
    {
        $ids = \array_map(
            function (ResolvedEvent $event): EventId {
                return $event->originalEvent()->eventId();
            },
            $events
        );

        $this->subscription->notifyEventsProcessed($ids);
    }

    /**
     * Acknowledge that a message have completed processing (this will tell the server it has been processed)
     * Note: There is no need to ack a message if you have Auto Ack enabled
     *
     * @param EventId $eventId
     *
     * @return void
     */
    public function acknowledgeEventId(EventId $eventId): void
    {
        $this->subscription->notifyEventsProcessed([$eventId]);
    }

    /**
     * Acknowledge that a message have completed processing (this will tell the server it has been processed)
     * Note: There is no need to ack a message if you have Auto Ack enabled
     *
     * @param EventId[] $eventIds
     *
     * @return void
     */
    public function acknowledgeMultipleEventIds(array $eventIds): void
    {
        $this->subscription->notifyEventsProcessed($eventIds);
    }

    /**
     * Mark a message failed processing. The server will be take action based upon the action paramter
     */
    public function fail(ResolvedEvent $event, PersistentSubscriptionNakEventAction $action, string $reason): void
    {
        $this->subscription->notifyEventsFailed([$event->originalEvent()->eventId()], $action, $reason);
    }

    /**
     * Mark n messages that have failed processing. The server will take action based upon the action parameter
     *
     * @param ResolvedEvent[] $events
     * @param PersistentSubscriptionNakEventAction $action
     * @param string $reason
     */
    public function failMultiple(array $events, PersistentSubscriptionNakEventAction $action, string $reason): void
    {
        $ids = \array_map(
            function (ResolvedEvent $event): EventId {
                return $event->originalEvent()->eventId();
            },
            $events
        );

        $this->subscription->notifyEventsFailed($ids, $action, $reason);
    }

    public function stop(int $timeout): void
    {
        // if (_verbose) _log.Debug("Persistent Subscription to {0}: requesting stop...", _streamId);
        $this->enqueueSubscriptionDropNotification(SubscriptionDropReason::userInitiated(), null);

        Loop::delay($timeout, function (): void {
            if (! $this->stopped) {
                throw new TimeoutException('Could not stop subscription in time');
            }
        });
    }

    private function enqueueSubscriptionDropNotification(SubscriptionDropReason $reason, ?Throwable $error): void
    {
        // if drop data was already set -- no need to enqueue drop again, somebody did that already
        $dropData = new DropData($reason, $error);

        if ($dropData !== $this->dropData) {
            $this->enqueue(
                new PersistentSubscriptionResolvedEvent(self::$dropSubscriptionEvent, null)
            );
        }
    }

    private function onSubscriptionDropped(
        EventStoreSubscription $subscription,
        SubscriptionDropReason $reason,
        Throwable $exception): void
    {
        $this->enqueueSubscriptionDropNotification($reason, $exception);
    }

    private function onEventAppeared(
        EventStoreSubscription $subscription,
        PersistentSubscriptionResolvedEvent $resolvedEvent
    ): Promise {
        $this->enqueue($resolvedEvent);

        return new Success();
    }

    private function enqueue(PersistentSubscriptionResolvedEvent $resolvedEvent): void
    {
        $this->queue[] = $resolvedEvent;

        if (! $this->isProcessing) {
            $this->isProcessing = true;

            Loop::defer(function (): Generator {
                yield $this->processQueue();
            });
        }
    }

    /** @return Promise<void> */
    private function processQueue(): Promise
    {
        return call(function (): Generator {
            do {
                if (null === $this->subscription) {
                    yield new Delayed(1000);
                } else {
                    while (! $this->queue->isEmpty()) {
                        /** @var PersistentSubscriptionResolvedEvent $e */
                        $e = $this->queue->dequeue();
                        if ($e->event() === self::$dropSubscriptionEvent) {
                            // drop subscription artificial ResolvedEvent
                            $this->dropSubscription($this->dropData->reason(), $this->dropData->error());

                            return null;
                        }

                        if (null !== $this->dropData) {
                            $this->dropSubscription($this->dropData->reason(), $this->dropData->error());

                            return null;
                        }

                        try {
                            yield ($this->eventAppeared)($this, $e->event(), $e->retryCount());

                            if ($this->autoAck) {
                                $this->subscription->notifyEventsProcessed([$e->originalEvent()->eventId()]);
                            }
                            /*
                            if (_verbose)
                                _log.Debug("Persistent Subscription to {0}: processed event ({1}, {2}, {3} @ {4}).",
                                    _streamId,
                                    e.Event.OriginalEvent.EventStreamId, e.Event.OriginalEvent.EventNumber, e.Event.OriginalEvent.EventType, e.Event.OriginalEventNumber);
                            */
                        } catch (Throwable $ex) {
                            //TODO GFY should we autonak here?
                            $this->dropSubscription(SubscriptionDropReason::eventHandlerException(), $ex);

                            return null;
                        }
                    }
                }
            } while (! $this->queue->isEmpty() && $this->isProcessing);

            $this->isProcessing = false;
        });
    }

    private function dropSubscription(SubscriptionDropReason $reason, Throwable $error): void
    {
        if ($this->isDropped) {
            /*
             * if (_verbose)
                _log.Debug("Persistent Subscription to {0}: dropping subscription, reason: {1} {2}.",
                    _streamId, reason, error == null ? string.Empty : error.ToString());
             */

            if (null !== $this->subscription) {
                $this->subscription->unsubscribe();
            }

            ($this->subscriptionDropped)($this, $reason, $error);

            $this->stopped = true;
        }
    }
}
