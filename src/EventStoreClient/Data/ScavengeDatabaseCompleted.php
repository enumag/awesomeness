<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: ClientMessageDtos.proto

namespace Prooph\EventStoreClient\Data;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Prooph.EventStoreClient.Data.ScavengeDatabaseCompleted</code>
 */
class ScavengeDatabaseCompleted extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.Prooph.EventStoreClient.Data.ScavengeDatabaseCompleted.ScavengeResult result = 1;</code>
     */
    private $result = 0;
    /**
     * Generated from protobuf field <code>string error = 2;</code>
     */
    private $error = '';
    /**
     * Generated from protobuf field <code>int32 total_time_ms = 3;</code>
     */
    private $total_time_ms = 0;
    /**
     * Generated from protobuf field <code>int64 total_space_saved = 4;</code>
     */
    private $total_space_saved = 0;

    public function __construct() {
        \GPBMetadata\ClientMessageDtos::initOnce();
        parent::__construct();
    }

    /**
     * Generated from protobuf field <code>.Prooph.EventStoreClient.Data.ScavengeDatabaseCompleted.ScavengeResult result = 1;</code>
     * @return int
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Generated from protobuf field <code>.Prooph.EventStoreClient.Data.ScavengeDatabaseCompleted.ScavengeResult result = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setResult($var)
    {
        GPBUtil::checkEnum($var, \Prooph\EventStoreClient\Data\ScavengeDatabaseCompleted_ScavengeResult::class);
        $this->result = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string error = 2;</code>
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Generated from protobuf field <code>string error = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setError($var)
    {
        GPBUtil::checkString($var, True);
        $this->error = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int32 total_time_ms = 3;</code>
     * @return int
     */
    public function getTotalTimeMs()
    {
        return $this->total_time_ms;
    }

    /**
     * Generated from protobuf field <code>int32 total_time_ms = 3;</code>
     * @param int $var
     * @return $this
     */
    public function setTotalTimeMs($var)
    {
        GPBUtil::checkInt32($var);
        $this->total_time_ms = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int64 total_space_saved = 4;</code>
     * @return int|string
     */
    public function getTotalSpaceSaved()
    {
        return $this->total_space_saved;
    }

    /**
     * Generated from protobuf field <code>int64 total_space_saved = 4;</code>
     * @param int|string $var
     * @return $this
     */
    public function setTotalSpaceSaved($var)
    {
        GPBUtil::checkInt64($var);
        $this->total_space_saved = $var;

        return $this;
    }

}

