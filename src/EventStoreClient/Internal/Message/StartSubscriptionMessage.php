<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient\Internal\Message;

use Amp\Deferred;
use Prooph\EventStoreClient\UserCredentials;

/** @internal  */
class StartSubscriptionMessage implements Message
{
    /** @var Deferred */
    private $deferred;
    /** @var string */
    private $streamId;
    /** @var bool */
    private $resolveTo;
    /** @var UserCredentials|null */
    private $userCredentials;
    /** @var callable */
    private $eventAppeared;
    /** @var callable */
    private $subscriptionDropped;
    /** @var int */
    private $maxRetries;
    /** @var int */
    private $timeout;

    /**
     * @param Deferred $deferred
     * @param string $streamId
     * @param bool $resolveTo
     * @param UserCredentials|null $userCredentials
     * @param callable(EventStoreSubscription $subscription, ResolvedEvent $event): Promise $eventAppeared
     * @param null|callable(EventStoreSubscription $subscription, SubscriptionDropReason $reason, Exception $exception): void $subscriptionDropped
     * @param int $maxRetries
     * @param int $timeout
     */
    public function __construct(
        Deferred $deferred,
        string $streamId,
        bool $resolveTo,
        ?UserCredentials $userCredentials,
        callable $eventAppeared,
        ?callable $subscriptionDropped,
        int $maxRetries,
        int $timeout
    ) {
        $this->deferred = $deferred;
        $this->streamId = $streamId;
        $this->resolveTo = $resolveTo;
        $this->userCredentials = $userCredentials;
        $this->eventAppeared = $eventAppeared;
        $this->subscriptionDropped = $subscriptionDropped ?? function (): void {
        };
        $this->maxRetries = $maxRetries;
        $this->timeout = $timeout;
    }

    public function deferred(): Deferred
    {
        return $this->deferred;
    }

    public function streamId(): string
    {
        return $this->streamId;
    }

    public function resolveTo(): bool
    {
        return $this->resolveTo;
    }

    public function userCredentials(): ?UserCredentials
    {
        return $this->userCredentials;
    }

    public function eventAppeared(): callable
    {
        return $this->eventAppeared;
    }

    public function subscriptionDropped(): callable
    {
        return $this->subscriptionDropped;
    }

    public function maxRetries(): int
    {
        return $this->maxRetries;
    }

    public function timeout(): int
    {
        return $this->timeout;
    }

    public function __toString(): string
    {
        return 'StartSubscriptionMessage';
    }
}
