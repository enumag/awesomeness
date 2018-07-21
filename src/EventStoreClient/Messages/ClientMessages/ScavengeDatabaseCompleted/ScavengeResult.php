<?php

declare(strict_types=1);
// Generated by the protocol buffer compiler.  DO NOT EDIT!
// source: ClientMessageDtos.proto

namespace Prooph\EventStoreClient\Messages\ClientMessages\ScavengeDatabaseCompleted;

/**
 * Protobuf type <code>Prooph.EventStoreClient.Messages.ClientMessages.ScavengeDatabaseCompleted.ScavengeResult</code>
 */
class ScavengeResult
{
    /**
     * Generated from protobuf enum <code>Success = 0;</code>
     */
    const Success = 0;
    /**
     * Generated from protobuf enum <code>InProgress = 1;</code>
     */
    const InProgress = 1;
    /**
     * Generated from protobuf enum <code>Failed = 2;</code>
     */
    const Failed = 2;
}

// Adding a class alias for backwards compatibility with the previous class name.
\class_alias(ScavengeResult::class, \Prooph\EventStoreClient\Messages\ClientMessages\ScavengeDatabaseCompleted_ScavengeResult::class);
