<?php

declare(strict_types=1);

namespace Prooph\EventStore\Messages;

class ResolvedEvent
{
    /** @var EventRecord */
    private $event;
    /** @var EventRecord|null */
    private $link;
    /** @var int */
    private $commitPosition;
    /** @var int */
    private $preparePosition;

    public function __construct(EventRecord $event, ?EventRecord $link, int $commitPosition, int $preparePosition)
    {
        $this->event = $event;
        $this->link = $link;
        $this->commitPosition = $commitPosition;
        $this->preparePosition = $preparePosition;
    }

    public function event(): EventRecord
    {
        return $this->event;
    }

    public function link(): ?EventRecord
    {
        return $this->link;
    }

    public function commitPosition(): int
    {
        return $this->commitPosition;
    }

    public function preparePosition(): int
    {
        return $this->preparePosition;
    }
}
