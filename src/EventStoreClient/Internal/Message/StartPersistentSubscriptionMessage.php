<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient\Internal\Message;

use Amp\Deferred;
use Prooph\EventStoreClient\UserCredentials;

/** @internal  */
class StartPersistentSubscriptionMessage implements Message
{
    /** @var Deferred */
    private $deferred;
    /** @var string */
    private $subscriptionId;
    /** @var string */
    private $streamId;
    /** @var int */
    private $bufferSize;
    /** @var UserCredentials */
    private $userCredentials;
    /** @var callable */
    private $eventAppeared;
    /** @var callable */
    private $subscriptionDropped;
    /** @var int */
    private $maxRetries;
    /** @var int */
    private $timeout;

    public function __construct(
        Deferred $deferred,
        string $subscriptionId,
        string $streamId,
        int $bufferSize,
        UserCredentials $userCredentials,
        callable $eventAppeared,
        ?callable $subscriptionDropped,
        int $maxRetries,
        int $timeout
    ) {
        $this->deferred = $deferred;
        $this->subscriptionId = $subscriptionId;
        $this->streamId = $streamId;
        $this->bufferSize = $bufferSize;
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

    public function subscriptionId(): string
    {
        return $this->subscriptionId;
    }

    public function streamId(): string
    {
        return $this->streamId;
    }

    public function bufferSize(): int
    {
        return $this->bufferSize;
    }

    public function userCredentials(): UserCredentials
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
        return 'StartPersistentSubscriptionMessage';
    }
}
