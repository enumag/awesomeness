<?php

declare(strict_types=1);

namespace Prooph\EventStore\Internal\Event;

use Prooph\EventStoreClient\Internal\EventStoreAsyncNodeConnection;

class ClientReconnectingEventArgs implements EventArgs
{
    /** @var EventStoreAsyncNodeConnection */
    private $connection;

    public function __construct(EventStoreAsyncNodeConnection $connection)
    {
        $this->connection = $connection;
    }

    public function connection(): EventStoreAsyncNodeConnection
    {
        return $this->connection;
    }
}
