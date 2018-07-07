<?php

declare(strict_types=1);

namespace Prooph\EventStore;

use Amp\Promise;

interface EventStoreAsyncConnection
{
    public function connectAsync(): Promise;

    public function close(): void;

    /** @return Promise<DeleteResult> */
    public function deleteStreamAsync(
        string $stream,
        bool $hardDelete,
        UserCredentials $userCredentials = null
    ): Promise;

    /**
     * @param string $stream
     * @param int $expectedVersion
     * @param null|UserCredentials $userCredentials
     * @param EventData[] $events
     * @return Promise<WriteResult>
     */
    public function appendToStreamAsync(
        string $stream,
        int $expectedVersion,
        array $events,
        UserCredentials $userCredentials = null
    ): Promise;

    /** @return Promise<EventReadResult> */
    public function readEventAsync(
        string $stream,
        int $eventNumber,
        bool $resolveLinkTos = true,
        UserCredentials $userCredentials = null
    ): Promise;

    /** @return Promise<StreamEventsSlice> */
    public function readStreamEventsForwardAsync(
        string $stream,
        int $start,
        int $count,
        bool $resolveLinkTos = true,
        UserCredentials $userCredentials = null
    ): Promise;

    /** @return Promise<StreamEventsSlice> */
    public function readStreamEventsBackwardAsync(
        string $stream,
        int $start,
        int $count,
        bool $resolveLinkTos = true,
        UserCredentials $userCredentials = null
    ): Promise;

    /** @return Promise<AllEventsSlice> */
    public function readAllEventsForward(
        Position $position,
        int $count,
        bool $resolveLinkTos = true,
        UserCredentials $userCredentials = null
    ): Promise;

    /** @return Promise<AllEventsSlice> */
    public function readAllEventsBackward(
        Position $position,
        int $count,
        bool $resolveLinkTos = true,
        UserCredentials $userCredentials = null
    ): Promise;

    /** @return Promise<WriteResult> */
    public function setStreamMetadataAsync(
        string $stream,
        int $expectedMetaStreamVersion,
        StreamMetadata $metadata,
        UserCredentials $userCredentials = null
    ): Promise;

    /** @return Promise<StreamMetadataResult> */
    public function getStreamMetadataAsync(string $stream, UserCredentials $userCredentials = null): Promise;

    /** @return Promise<WriteResult> */
    public function setSystemSettingsAsync(SystemSettings $settings, UserCredentials $userCredentials = null): Promise;

    // @todo event handlers
}
