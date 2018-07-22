<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient\ClientOperations;

use Prooph\EventStoreClient\Data\SubscriptionDropReason;
use Prooph\EventStoreClient\Internal\SystemData\InspectionResult;
use Prooph\EventStoreClient\Transport\Tcp\TcpPackage;
use Prooph\EventStoreClient\Transport\Tcp\TcpPackageConnection;
use Throwable;

/** @internal  */
interface SubscriptionOperation
{
    public function dropSubscription(
        SubscriptionDropReason $reason,
        Throwable $exception = null,
        TcpPackageConnection $connection = null
    ): void;

    public function connectionClosed(): void;

    public function inspectPackage(TcpPackage $package): InspectionResult;

    public function subscribe(string $correlationId, TcpPackageConnection $connection): bool;
}