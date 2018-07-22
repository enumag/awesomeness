<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient\Internal;

use DateTimeImmutable;
use Prooph\EventStoreClient\ClientOperations\SubscriptionOperation;

/** @internal  */
class SubscriptionItem
{
    /** @var SubscriptionOperation */
    private $operation;
    /** @var int */
    private $maxRetries;
    /** @var int */
    private $timeout;
    /** @var DateTimeImmutable */
    private $created;

    /** @var string */
    private $connectionId;
    /** @var string */
    private $correlationId;
    /** @var bool */
    private $isSubscribed;
    /** @var int */
    private $retryCount;
    /** @var DateTimeImmutable */
    private $lastUpdated;

    public function __construct(SubscriptionOperation $operation, int $maxRetries, int $timeout)
    {
        $this->operation = $operation;
        $this->maxRetries = $maxRetries;
        $this->timeout = $timeout;
        $this->created = DateTimeUtil::utcNow();
        $this->correlationId = UuidGenerator::generate();
        $this->retryCount = 0;
        $this->lastUpdated = $this->created;
        $this->isSubscribed = false;
    }

    public function operation(): SubscriptionOperation
    {
        return $this->operation;
    }

    public function maxRetries(): int
    {
        return $this->maxRetries;
    }

    public function timeout(): int
    {
        return $this->timeout;
    }

    public function created(): DateTimeImmutable
    {
        return $this->created;
    }

    public function connectionId(): string
    {
        return $this->connectionId;
    }

    public function correlationId(): string
    {
        return $this->correlationId;
    }

    public function isSubscribed(): bool
    {
        return $this->isSubscribed;
    }

    public function retryCount(): int
    {
        return $this->retryCount;
    }

    public function lastUpdated(): DateTimeImmutable
    {
        return $this->lastUpdated;
    }

    public function setConnectionId(string $connectionId): void
    {
        $this->connectionId = $connectionId;
    }

    public function setCorrelationId(string $correlationId): void
    {
        $this->correlationId = $correlationId;
    }

    public function setIsSubscribed(bool $isSubscribed): void
    {
        $this->isSubscribed = $isSubscribed;
    }

    public function incRetryCount(): void
    {
        ++$this->retryCount;
    }

    public function setLastUpdated(DateTimeImmutable $lastUpdated): void
    {
        $this->lastUpdated = $lastUpdated;
    }

    public function __toString(): string
    {
        return \sprintf(
            'Subscription %s (%s): %s, is subscribed: %s, retry count: %d, created: %s, last updated: %s',
            $this->operation->name(),
            $this->correlationId,
            $this->operation,
            $this->isSubscribed ? 'yes' : 'no',
            $this->retryCount,
            DateTimeUtil::format($this->created),
            DateTimeUtil::format($this->lastUpdated)
        );
    }
}
