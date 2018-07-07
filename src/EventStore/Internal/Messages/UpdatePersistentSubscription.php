<?php

declare(strict_types=1);
// Generated by the protocol buffer compiler.  DO NOT EDIT!
// source: ClientMessageDtos.proto

namespace Prooph\EventStore\Internal\Messages;

use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Prooph.EventStore.Internal.Messages.UpdatePersistentSubscription</code>
 */
class UpdatePersistentSubscription extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string subscription_group_name = 1;</code>
     */
    private $subscription_group_name = '';
    /**
     * Generated from protobuf field <code>string event_stream_id = 2;</code>
     */
    private $event_stream_id = '';
    /**
     * Generated from protobuf field <code>bool resolve_link_tos = 3;</code>
     */
    private $resolve_link_tos = false;
    /**
     * Generated from protobuf field <code>int64 start_from = 4;</code>
     */
    private $start_from = 0;
    /**
     * Generated from protobuf field <code>int32 message_timeout_milliseconds = 5;</code>
     */
    private $message_timeout_milliseconds = 0;
    /**
     * Generated from protobuf field <code>bool record_statistics = 6;</code>
     */
    private $record_statistics = false;
    /**
     * Generated from protobuf field <code>int32 live_buffer_size = 7;</code>
     */
    private $live_buffer_size = 0;
    /**
     * Generated from protobuf field <code>int32 read_batch_size = 8;</code>
     */
    private $read_batch_size = 0;
    /**
     * Generated from protobuf field <code>int32 buffer_size = 9;</code>
     */
    private $buffer_size = 0;
    /**
     * Generated from protobuf field <code>int32 max_retry_count = 10;</code>
     */
    private $max_retry_count = 0;
    /**
     * Generated from protobuf field <code>bool prefer_round_robin = 11;</code>
     */
    private $prefer_round_robin = false;
    /**
     * Generated from protobuf field <code>int32 checkpoint_after_time = 12;</code>
     */
    private $checkpoint_after_time = 0;
    /**
     * Generated from protobuf field <code>int32 checkpoint_max_count = 13;</code>
     */
    private $checkpoint_max_count = 0;
    /**
     * Generated from protobuf field <code>int32 checkpoint_min_count = 14;</code>
     */
    private $checkpoint_min_count = 0;
    /**
     * Generated from protobuf field <code>int32 subscriber_max_count = 15;</code>
     */
    private $subscriber_max_count = 0;
    /**
     * Generated from protobuf field <code>string named_consumer_strategy = 16;</code>
     */
    private $named_consumer_strategy = '';

    public function __construct()
    {
        \GPBMetadata\ClientMessageDtos::initOnce();
        parent::__construct();
    }

    /**
     * Generated from protobuf field <code>string subscription_group_name = 1;</code>
     * @return string
     */
    public function getSubscriptionGroupName()
    {
        return $this->subscription_group_name;
    }

    /**
     * Generated from protobuf field <code>string subscription_group_name = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setSubscriptionGroupName($var)
    {
        GPBUtil::checkString($var, true);
        $this->subscription_group_name = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string event_stream_id = 2;</code>
     * @return string
     */
    public function getEventStreamId()
    {
        return $this->event_stream_id;
    }

    /**
     * Generated from protobuf field <code>string event_stream_id = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setEventStreamId($var)
    {
        GPBUtil::checkString($var, true);
        $this->event_stream_id = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bool resolve_link_tos = 3;</code>
     * @return bool
     */
    public function getResolveLinkTos()
    {
        return $this->resolve_link_tos;
    }

    /**
     * Generated from protobuf field <code>bool resolve_link_tos = 3;</code>
     * @param bool $var
     * @return $this
     */
    public function setResolveLinkTos($var)
    {
        GPBUtil::checkBool($var);
        $this->resolve_link_tos = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int64 start_from = 4;</code>
     * @return int|string
     */
    public function getStartFrom()
    {
        return $this->start_from;
    }

    /**
     * Generated from protobuf field <code>int64 start_from = 4;</code>
     * @param int|string $var
     * @return $this
     */
    public function setStartFrom($var)
    {
        GPBUtil::checkInt64($var);
        $this->start_from = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 message_timeout_milliseconds = 5;</code>
     * @return int
     */
    public function getMessageTimeoutMilliseconds()
    {
        return $this->message_timeout_milliseconds;
    }

    /**
     * Generated from protobuf field <code>int32 message_timeout_milliseconds = 5;</code>
     * @param int $var
     * @return $this
     */
    public function setMessageTimeoutMilliseconds($var)
    {
        GPBUtil::checkInt32($var);
        $this->message_timeout_milliseconds = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bool record_statistics = 6;</code>
     * @return bool
     */
    public function getRecordStatistics()
    {
        return $this->record_statistics;
    }

    /**
     * Generated from protobuf field <code>bool record_statistics = 6;</code>
     * @param bool $var
     * @return $this
     */
    public function setRecordStatistics($var)
    {
        GPBUtil::checkBool($var);
        $this->record_statistics = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 live_buffer_size = 7;</code>
     * @return int
     */
    public function getLiveBufferSize()
    {
        return $this->live_buffer_size;
    }

    /**
     * Generated from protobuf field <code>int32 live_buffer_size = 7;</code>
     * @param int $var
     * @return $this
     */
    public function setLiveBufferSize($var)
    {
        GPBUtil::checkInt32($var);
        $this->live_buffer_size = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 read_batch_size = 8;</code>
     * @return int
     */
    public function getReadBatchSize()
    {
        return $this->read_batch_size;
    }

    /**
     * Generated from protobuf field <code>int32 read_batch_size = 8;</code>
     * @param int $var
     * @return $this
     */
    public function setReadBatchSize($var)
    {
        GPBUtil::checkInt32($var);
        $this->read_batch_size = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 buffer_size = 9;</code>
     * @return int
     */
    public function getBufferSize()
    {
        return $this->buffer_size;
    }

    /**
     * Generated from protobuf field <code>int32 buffer_size = 9;</code>
     * @param int $var
     * @return $this
     */
    public function setBufferSize($var)
    {
        GPBUtil::checkInt32($var);
        $this->buffer_size = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 max_retry_count = 10;</code>
     * @return int
     */
    public function getMaxRetryCount()
    {
        return $this->max_retry_count;
    }

    /**
     * Generated from protobuf field <code>int32 max_retry_count = 10;</code>
     * @param int $var
     * @return $this
     */
    public function setMaxRetryCount($var)
    {
        GPBUtil::checkInt32($var);
        $this->max_retry_count = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bool prefer_round_robin = 11;</code>
     * @return bool
     */
    public function getPreferRoundRobin()
    {
        return $this->prefer_round_robin;
    }

    /**
     * Generated from protobuf field <code>bool prefer_round_robin = 11;</code>
     * @param bool $var
     * @return $this
     */
    public function setPreferRoundRobin($var)
    {
        GPBUtil::checkBool($var);
        $this->prefer_round_robin = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 checkpoint_after_time = 12;</code>
     * @return int
     */
    public function getCheckpointAfterTime()
    {
        return $this->checkpoint_after_time;
    }

    /**
     * Generated from protobuf field <code>int32 checkpoint_after_time = 12;</code>
     * @param int $var
     * @return $this
     */
    public function setCheckpointAfterTime($var)
    {
        GPBUtil::checkInt32($var);
        $this->checkpoint_after_time = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 checkpoint_max_count = 13;</code>
     * @return int
     */
    public function getCheckpointMaxCount()
    {
        return $this->checkpoint_max_count;
    }

    /**
     * Generated from protobuf field <code>int32 checkpoint_max_count = 13;</code>
     * @param int $var
     * @return $this
     */
    public function setCheckpointMaxCount($var)
    {
        GPBUtil::checkInt32($var);
        $this->checkpoint_max_count = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 checkpoint_min_count = 14;</code>
     * @return int
     */
    public function getCheckpointMinCount()
    {
        return $this->checkpoint_min_count;
    }

    /**
     * Generated from protobuf field <code>int32 checkpoint_min_count = 14;</code>
     * @param int $var
     * @return $this
     */
    public function setCheckpointMinCount($var)
    {
        GPBUtil::checkInt32($var);
        $this->checkpoint_min_count = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 subscriber_max_count = 15;</code>
     * @return int
     */
    public function getSubscriberMaxCount()
    {
        return $this->subscriber_max_count;
    }

    /**
     * Generated from protobuf field <code>int32 subscriber_max_count = 15;</code>
     * @param int $var
     * @return $this
     */
    public function setSubscriberMaxCount($var)
    {
        GPBUtil::checkInt32($var);
        $this->subscriber_max_count = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string named_consumer_strategy = 16;</code>
     * @return string
     */
    public function getNamedConsumerStrategy()
    {
        return $this->named_consumer_strategy;
    }

    /**
     * Generated from protobuf field <code>string named_consumer_strategy = 16;</code>
     * @param string $var
     * @return $this
     */
    public function setNamedConsumerStrategy($var)
    {
        GPBUtil::checkString($var, true);
        $this->named_consumer_strategy = $var;

        return $this;
    }
}
