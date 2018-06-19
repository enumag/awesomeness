<?php

declare(strict_types=1);

namespace Prooph\EventStore\ProjectionManagement;

/** @internal */
class ProjectionDetails
{
    /** @var int */
    private $coreProcessingTime;
    /** @var int */
    private $version;
    /** @var int */
    private $epoch;
    /** @var string */
    private $effectiveName;
    /** @var int */
    private $writesInProgress;
    /** @var int */
    private $readsInProgress;
    /** @var int */
    private $partitionsCached;
    /** @var string */
    private $status;
    /** @var string */
    private $stateReason;
    /** @var string */
    private $name;
    /** @var string */
    private $mode;
    /** @var string */
    private $position;
    /** @var int */
    private $progress;
    /** @var string */
    private $lastCheckpoint;
    /** @var int */
    private $eventsProcessedAfterRestart;
    /** @var string */
    private $statusUrl;
    /** @var string */
    private $stateUrl;
    /** @var string */
    private $resultUrl;
    /** @var string */
    private $queryUrl;
    /** @var string */
    private $enableCommandUrl;
    /** @var string */
    private $disableCommandUrl;
    /** @var string */
    private $checkpointStatus;
    /** @var int */
    private $bufferedEvents;
    /** @var int */
    private $writePendingEventsBeforeCheckpoint;
    /** @var int */
    private $writePendingEventsAfterCheckpoint;

    public function __construct(
        int $coreProcessingTime,
        int $version,
        int $epoch,
        string $effectiveName,
        int $writesInProgress,
        int $readsInProgress,
        int $partitionsCached,
        string $status,
        string $stateReason,
        string $name,
        string $mode,
        string $position,
        int $progress,
        string $lastCheckpoint,
        int $eventsProcessedAfterRestart,
        string $statusUrl,
        string $stateUrl,
        string $resultUrl,
        string $queryUrl,
        string $enableCommandUrl,
        string $disableCommandUrl,
        string $checkpointStatus,
        int $bufferedEvents,
        int $writePendingEventsBeforeCheckpoint,
        int $writePendingEventsAfterCheckpoint
    ) {
        $this->coreProcessingTime = $coreProcessingTime;
        $this->version = $version;
        $this->epoch = $epoch;
        $this->effectiveName = $effectiveName;
        $this->writesInProgress = $writesInProgress;
        $this->readsInProgress = $readsInProgress;
        $this->partitionsCached = $partitionsCached;
        $this->status = $status;
        $this->stateReason = $stateReason;
        $this->name = $name;
        $this->mode = $mode;
        $this->position = $position;
        $this->progress = $progress;
        $this->lastCheckpoint = $lastCheckpoint;
        $this->eventsProcessedAfterRestart = $eventsProcessedAfterRestart;
        $this->statusUrl = $statusUrl;
        $this->stateUrl = $stateUrl;
        $this->resultUrl = $resultUrl;
        $this->queryUrl = $queryUrl;
        $this->enableCommandUrl = $enableCommandUrl;
        $this->disableCommandUrl = $disableCommandUrl;
        $this->checkpointStatus = $checkpointStatus;
        $this->bufferedEvents = $bufferedEvents;
        $this->writePendingEventsBeforeCheckpoint = $writePendingEventsBeforeCheckpoint;
        $this->writePendingEventsAfterCheckpoint = $writePendingEventsAfterCheckpoint;
    }

    public function coreProcessingTime(): int
    {
        return $this->coreProcessingTime;
    }

    public function version(): int
    {
        return $this->version;
    }

    public function epoch(): int
    {
        return $this->epoch;
    }

    public function effectiveName(): string
    {
        return $this->effectiveName;
    }

    public function writesInProgress(): int
    {
        return $this->writesInProgress;
    }

    public function readsInProgress(): int
    {
        return $this->readsInProgress;
    }

    public function partitionsCached(): int
    {
        return $this->partitionsCached;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function stateReason(): string
    {
        return $this->stateReason;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function mode(): string
    {
        return $this->mode;
    }

    public function position(): string
    {
        return $this->position;
    }

    public function progress(): int
    {
        return $this->progress;
    }

    public function lastCheckpoint(): string
    {
        return $this->lastCheckpoint;
    }

    public function eventsProcessedAfterRestart(): int
    {
        return $this->eventsProcessedAfterRestart;
    }

    public function statusUrl(): string
    {
        return $this->statusUrl;
    }

    public function stateUrl(): string
    {
        return $this->stateUrl;
    }

    public function resultUrl(): string
    {
        return $this->resultUrl;
    }

    public function queryUrl(): string
    {
        return $this->queryUrl;
    }

    public function enableCommandUrl(): string
    {
        return $this->enableCommandUrl;
    }

    public function disableCommandUrl(): string
    {
        return $this->disableCommandUrl;
    }

    public function checkpointStatus(): string
    {
        return $this->checkpointStatus;
    }

    public function bufferedEvents(): int
    {
        return $this->bufferedEvents;
    }

    public function writePendingEventsBeforeCheckpoint(): int
    {
        return $this->writePendingEventsBeforeCheckpoint;
    }

    public function writePendingEventsAfterCheckpoint(): int
    {
        return $this->writePendingEventsAfterCheckpoint;
    }

    public function toArray(): array
    {
        return [
            'coreProcessingTime' => $this->coreProcessingTime,
            'version' => $this->version,
            'epoch' => $this->epoch,
            'effectiveName' => $this->effectiveName,
            'writesInProgress' => $this->writesInProgress,
            'readsInProgress' => $this->readsInProgress,
            'partitionsCached' => $this->partitionsCached,
            'status' => $this->status,
            'stateReason' => $this->stateReason,
            'name' => $this->name,
            'mode' => $this->mode,
            'position' => $this->position,
            'progress' => $this->progress,
            'lastCheckpoint' => $this->lastCheckpoint,
            'eventsProcessedAfterRestart' => $this->eventsProcessedAfterRestart,
            'statusUrl' => $this->statusUrl,
            'stateUrl' => $this->stateUrl,
            'resultUrl' => $this->resultUrl,
            'queryUrl' => $this->queryUrl,
            'enableCommandUrl' => $this->enableCommandUrl,
            'disableCommandUrl' => $this->disableCommandUrl,
            'checkpointStatus' => $this->checkpointStatus,
            'bufferedEvents' => $this->bufferedEvents,
            'writePendingEventsBeforeCheckpoint' => $this->writePendingEventsBeforeCheckpoint,
            'writePendingEventsAfterCheckpoint' => $this->writePendingEventsAfterCheckpoint,
        ];
    }
}
