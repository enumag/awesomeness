<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient;

use Amp\Loop;
use Amp\Promise;
use Amp\Success;
use Prooph\EventStoreClient\Internal\EventStoreCatchUpSubscription;
use Prooph\EventStoreClient\Internal\StopWatch;

require __DIR__ . '/../../vendor/autoload.php';

Loop::run(function () {
    $builder = new ConnectionSettingsBuilder();
    $builder->enableVerboseLogging();
    $builder->useConsoleLogger();

    $connection = EventStoreConnectionBuilder::createAsyncFromIpEndPoint(
        new IpEndPoint('localhost', 1113),
        $builder->build()
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

    $connection->subscribeToStreamFrom(
        'opium2-bar',
        null,
        CatchUpSubscriptionSettings::default(),
        function (EventStoreCatchUpSubscription $subscription, ResolvedEvent $event) use ($stopWatch, &$i): Promise {
            return new Success();
        },
        function (EventStoreCatchUpSubscription $subscription): void {
            echo 'liveProcessingStarted on ' . $subscription->streamId() . PHP_EOL;
        },
        function (EventStoreCatchUpSubscription $subscription, SubscriptionDropReason $reason, \Throwable $exception): void {
            echo 'dropped with reason: ' . $reason->name() . PHP_EOL;
            echo 'ex: ' . $exception->getMessage() . PHP_EOL;
        }
    );
});
