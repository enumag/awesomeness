<?php

declare(strict_types=1);
// Generated by the protocol buffer compiler.  DO NOT EDIT!
// source: ClientMessageDtos.proto

namespace Prooph\EventStoreClient\Messages\ClientMessages;

use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Prooph.EventStoreClient.Messages.ClientMessages.IdentifyClient</code>
 */
class IdentifyClient extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>int32 version = 1;</code>
     */
    private $version = 0;
    /**
     * Generated from protobuf field <code>string connection_name = 2;</code>
     */
    private $connection_name = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $version
     *     @type string $connection_name
     * }
     */
    public function __construct($data = null)
    {
        \GPBMetadata\ClientMessageDtos::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>int32 version = 1;</code>
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Generated from protobuf field <code>int32 version = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setVersion($var)
    {
        GPBUtil::checkInt32($var);
        $this->version = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string connection_name = 2;</code>
     * @return string
     */
    public function getConnectionName()
    {
        return $this->connection_name;
    }

    /**
     * Generated from protobuf field <code>string connection_name = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setConnectionName($var)
    {
        GPBUtil::checkString($var, true);
        $this->connection_name = $var;

        return $this;
    }
}