<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: ClientMessageDtos.proto

namespace Prooph\EventStoreClient\Data;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Prooph.EventStoreClient.Data.CreatePersistentSubscriptionCompleted</code>
 */
class CreatePersistentSubscriptionCompleted extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.Prooph.EventStoreClient.Data.CreatePersistentSubscriptionCompleted.CreatePersistentSubscriptionResult result = 1;</code>
     */
    private $result = 0;
    /**
     * Generated from protobuf field <code>string reason = 2;</code>
     */
    private $reason = '';

    public function __construct() {
        \GPBMetadata\ClientMessageDtos::initOnce();
        parent::__construct();
    }

    /**
     * Generated from protobuf field <code>.Prooph.EventStoreClient.Data.CreatePersistentSubscriptionCompleted.CreatePersistentSubscriptionResult result = 1;</code>
     * @return int
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Generated from protobuf field <code>.Prooph.EventStoreClient.Data.CreatePersistentSubscriptionCompleted.CreatePersistentSubscriptionResult result = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setResult($var)
    {
        GPBUtil::checkEnum($var, \Prooph\EventStoreClient\Data\CreatePersistentSubscriptionCompleted_CreatePersistentSubscriptionResult::class);
        $this->result = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string reason = 2;</code>
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Generated from protobuf field <code>string reason = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setReason($var)
    {
        GPBUtil::checkString($var, True);
        $this->reason = $var;

        return $this;
    }

}

