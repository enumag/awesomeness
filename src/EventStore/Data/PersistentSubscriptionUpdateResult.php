<?php

declare(strict_types=1);

namespace Prooph\EventStore\Data;

class PersistentSubscriptionUpdateResult
{
    /** @var PersistentSubscriptionUpdateStatus */
    private $status;

    /** @internal */
    public function __construct(PersistentSubscriptionUpdateStatus $status)
    {
        $this->status = $status;
    }

    public function status(): PersistentSubscriptionUpdateStatus
    {
        return $this->status;
    }
}
