<?php

declare(strict_types=1);
// Generated by the protocol buffer compiler.  DO NOT EDIT!
// source: ClientMessageDtos.proto

namespace Prooph\EventStore\Internal\Messages;

/**
 * Protobuf enum <code>Prooph\EventStore\Internal\Messages\ReadAllEventsCompleted\ReadAllResult</code>
 */
class ReadAllEventsCompleted_ReadAllResult
{
    /**
     * Generated from protobuf enum <code>Success = 0;</code>
     */
    const Success = 0;
    /**
     * Generated from protobuf enum <code>NotModified = 1;</code>
     */
    const NotModified = 1;
    /**
     * Generated from protobuf enum <code>Error = 2;</code>
     */
    const Error = 2;
    /**
     * Generated from protobuf enum <code>AccessDenied = 3;</code>
     */
    const AccessDenied = 3;
}
