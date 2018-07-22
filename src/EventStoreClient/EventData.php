<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient;

/**
 * Represents an event to be written.
 */
class EventData
{
    /** @var EventId */
    private $eventId;
    /** @var string */
    private $eventType;
    /** @var bool */
    private $isJson;
    /** @var string */
    private $data;
    /** @var string */
    private $metaData;

    public function __construct(?EventId $eventId, string $eventType, bool $isJson, string $data = '', string $metaData = '')
    {
        if (null === $eventId) {
            $eventId = EventId::generate();
        }

        $this->eventId = $eventId;
        $this->eventType = $eventType;
        $this->isJson = $isJson;
        $this->data = $data;
        $this->metaData = $metaData;
    }

    public function eventId(): EventId
    {
        return $this->eventId;
    }

    public function eventType(): string
    {
        return $this->eventType;
    }

    public function isJson(): bool
    {
        return $this->isJson;
    }

    public function data(): string
    {
        return $this->data;
    }

    public function metaData(): string
    {
        return $this->metaData;
    }
}
