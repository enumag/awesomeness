<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient;

use Amp\Loop;
use Amp\Promise;
use Amp\Success;
use Prooph\EventStoreClient\Data\ResolvedEvent;
use Prooph\EventStoreClient\Data\SubscriptionDropReason;
use Prooph\EventStoreClient\Data\UserCredentials;
use Prooph\EventStoreClient\IpEndPoint;
use Prooph\EventStoreClient\Internal\StopWatch;
use Prooph\EventStoreClient\Internal\VolatileEventStoreSubscription;

require __DIR__ . '/../../vendor/autoload.php';

Loop::run(function () {
    $connection = EventStoreConnection::createAsyncFromIpEndPoint(
        new IpEndPoint('localhost', 1113)
    );

    $connection->onConnected(function (): void {
        echo 'connected' . PHP_EOL;
    });

    $connection->onClosed(function (): void {
        echo 'connection closed' . PHP_EOL;
    });

    yield $connection->connectAsync();

    $stopWatch = StopWatch::startNew();
    $i = 0;

    $subscription = yield $connection->subscribeToStreamAsync(
        'opium2-bar',
        true,
        function (VolatileEventStoreSubscription $subscription, ResolvedEvent $event) use ($stopWatch, &$i): Promise {
            echo 'incoming event: ' . $event->originalEventNumber() . '@' . $event->originalStreamName() . PHP_EOL;
            echo 'data: ' . $event->originalEvent()->data() . PHP_EOL;
            echo 'no: ' . ++$i . ', elapsed: ' . $stopWatch->elapsed() . PHP_EOL;

            return new Success();
        },
        function (VolatileEventStoreSubscription $subscription, SubscriptionDropReason $reason, \Throwable $exception): void {
            echo 'dropped with reason: ' . $reason->name() . PHP_EOL;
            echo 'ex: ' . $exception->getMessage() . PHP_EOL;
        },
        new UserCredentials('admin', 'changeit')
    );

    /** @var VolatileEventStoreSubscription $subscription */
    echo 'last event number: ' . $subscription->lastEventNumber() . PHP_EOL;
});
