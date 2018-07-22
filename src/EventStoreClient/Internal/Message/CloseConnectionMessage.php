<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient\Internal\Message;

use Throwable;

/** @internal */
class CloseConnectionMessage implements Message
{
    /** @var string */
    private $reason;
    /** @var Throwable|null */
    private $exception;

    public function __construct(string $reason, Throwable $exception = null)
    {
        $this->reason = $reason;
        $this->exception = $exception;
    }

    public function reason(): string
    {
        return $this->reason;
    }

    public function exception(): ?Throwable
    {
        return $this->exception;
    }

    public function __toString(): string
    {
        return 'CloseConnectionMessage';
    }
}
