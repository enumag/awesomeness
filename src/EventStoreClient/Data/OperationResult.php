<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: ClientMessageDtos.proto

namespace Prooph\EventStoreClient\Data;

/**
 * Protobuf enum <code>Prooph\EventStoreClient\Data\OperationResult</code>
 */
class OperationResult
{
    /**
     * Generated from protobuf enum <code>Success = 0;</code>
     */
    const Success = 0;
    /**
     * Generated from protobuf enum <code>PrepareTimeout = 1;</code>
     */
    const PrepareTimeout = 1;
    /**
     * Generated from protobuf enum <code>CommitTimeout = 2;</code>
     */
    const CommitTimeout = 2;
    /**
     * Generated from protobuf enum <code>ForwardTimeout = 3;</code>
     */
    const ForwardTimeout = 3;
    /**
     * Generated from protobuf enum <code>WrongExpectedVersion = 4;</code>
     */
    const WrongExpectedVersion = 4;
    /**
     * Generated from protobuf enum <code>StreamDeleted = 5;</code>
     */
    const StreamDeleted = 5;
    /**
     * Generated from protobuf enum <code>InvalidTransaction = 6;</code>
     */
    const InvalidTransaction = 6;
    /**
     * Generated from protobuf enum <code>AccessDenied = 7;</code>
     */
    const AccessDenied = 7;
}

