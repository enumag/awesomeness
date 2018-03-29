<?php

declare(strict_types=1);

namespace Prooph\EventStore\Messages;

/** @internal */
final class DeletePersistentSubscriptionCompleted
{
    /** @var DeletePersistentSubscriptionResult */
    private $result;
    /** @var string|null */
    private $reason;

    public function __construct(DeletePersistentSubscriptionResult $result, ?string $reason)
    {
        $this->result = $result;
        $this->reason = $reason;
    }

    public function result(): DeletePersistentSubscriptionResult
    {
        return $this->result;
    }

    public function reason(): ?string
    {
        return $this->reason;
    }
}