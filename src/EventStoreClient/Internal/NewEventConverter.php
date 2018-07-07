<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient\Internal;

use Prooph\EventStore\EventData;
use Prooph\EventStore\Internal\Messages\NewEvent;

/** @internal */
class NewEventConverter
{
    public static function convert(EventData $eventData): NewEvent
    {
        $event = new NewEvent();

        if ($eventData->isJson()) {
            $contentType = 1;
        } else {
            $contentType = 2;
        }

        $event->setEventId($eventData->eventId()->toBinary());
        $event->setDataContentType($contentType);
        $event->setMetadataContentType($contentType);
        $event->setData($eventData->data());
        $event->setMetadata($eventData->metaData());
        $event->setEventType($eventData->eventType());

        return $event;
    }
}