<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient\Data;

class DeleteResult
{
    /** @var Position */
    private $logPosition;

    public function __construct(Position $logPosition)
    {
        $this->logPosition = $logPosition;
    }

    public function logPosition(): Position
    {
        return $this->logPosition;
    }
}
