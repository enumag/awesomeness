<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient;

class AllEventsSlice
{
    /** @var ReadDirection */
    private $readDirection;
    /** @var Position */
    private $fromPosition;
    /** @var Position */
    private $nextPosition;
    /** @var ResolvedEvent[] */
    private $events;
    /** @var bool */
    private $isEndOfStream;

    /**
     * @internal
     *
     * @param ReadDirection $readDirection
     * @param Position $fromPosition
     * @param Position $nextPosition
     * @param ResolvedEvent[] $events
     */
    public function __construct(
        ReadDirection $readDirection,
        Position $fromPosition,
        Position $nextPosition,
        array $events
    ) {
        $this->readDirection = $readDirection;
        $this->fromPosition = $fromPosition;
        $this->nextPosition = $nextPosition;
        $this->events = $events;
        $this->isEndOfStream = \count($events) === 0;
    }

    public function readDirection(): ReadDirection
    {
        return $this->readDirection;
    }

    public function fromPosition(): Position
    {
        return $this->fromPosition;
    }

    public function nextPosition(): Position
    {
        return $this->nextPosition;
    }

    /** @return ResolvedEvent[] */
    public function events(): array
    {
        return $this->events;
    }

    public function isEndOfStream(): bool
    {
        return $this->isEndOfStream;
    }
}
