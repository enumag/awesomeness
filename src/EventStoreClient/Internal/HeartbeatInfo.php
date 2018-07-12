<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient\Internal;

/** @internal */
class HeartbeatInfo
{
    /** @var int */
    private $lastPackageNumber;
    /** @var bool */
    private $isIntervalStage;
    /** @var int */
    private $timestamp;

    public function __construct(int $lastPackageNumber, bool $isIntervalStage, int $timestamp)
    {
        $this->lastPackageNumber = $lastPackageNumber;
        $this->isIntervalStage = $isIntervalStage;
        $this->timestamp = $timestamp;
    }

    public function lastPackageNumber(): int
    {
        return $this->lastPackageNumber;
    }

    public function isIntervalStage(): bool
    {
        return $this->isIntervalStage;
    }

    public function timestamp(): int
    {
        return $this->timestamp;
    }
}
