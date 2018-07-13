<?php

// this file is auto-generated by prolic/fpp
// don't edit this file manually

declare(strict_types=1);

namespace Prooph\EventStoreClient\Internal;

use Amp\Promise;
use Prooph\EventStore\IpEndPoint;

/** @internal */
interface EndPointDiscoverer
{
    /**
     * @param IpEndPoint|null $failedTcpEndPoint The recently failed endpoint
     * @return Promise<NodeEndPoints>
     */
    public function discoverAsync(?IpEndPoint $failedTcpEndPoint): Promise;
}
