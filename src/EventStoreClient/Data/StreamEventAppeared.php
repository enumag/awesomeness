<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: ClientMessageDtos.proto

namespace Prooph\EventStoreClient\Data;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Prooph.EventStoreClient.Data.StreamEventAppeared</code>
 */
class StreamEventAppeared extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.Prooph.EventStoreClient.Data.ResolvedEvent event = 1;</code>
     */
    private $event = null;

    public function __construct() {
        \GPBMetadata\ClientMessageDtos::initOnce();
        parent::__construct();
    }

    /**
     * Generated from protobuf field <code>.Prooph.EventStoreClient.Data.ResolvedEvent event = 1;</code>
     * @return \Prooph\EventStoreClient\Data\ResolvedEvent
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Generated from protobuf field <code>.Prooph.EventStoreClient.Data.ResolvedEvent event = 1;</code>
     * @param \Prooph\EventStoreClient\Data\ResolvedEvent $var
     * @return $this
     */
    public function setEvent($var)
    {
        GPBUtil::checkMessage($var, \Prooph\EventStoreClient\Data\ResolvedEvent::class);
        $this->event = $var;

        return $this;
    }

}

