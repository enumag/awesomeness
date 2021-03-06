<?php

declare(strict_types=1);

namespace Prooph\EventStore\Internal\Event;

use Prooph\EventStoreClient\Internal\EventStoreAsyncNodeConnection;
use Prooph\EventStoreClient\IpEndPoint;

class ClientConnectionEventArgs implements EventArgs
{
    /** @var EventStoreAsyncNodeConnection */
    private $connection;
    /** @var IpEndPoint */
    private $remoteEndPoint;

    public function __construct(EventStoreAsyncNodeConnection $connection, IpEndPoint $remoteEndPoint)
    {
        $this->connection = $connection;
        $this->remoteEndPoint = $remoteEndPoint;
    }

    public function connection(): EventStoreAsyncNodeConnection
    {
        return $this->connection;
    }

    public function remoteEndPoint(): IpEndPoint
    {
        return $this->remoteEndPoint;
    }
}
