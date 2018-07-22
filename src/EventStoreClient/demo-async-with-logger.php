<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient;

use Amp\Loop;

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

    $slice = yield $connection->readStreamEventsForwardAsync(
        'opium2-bar',
        10,
        2,
        true
    );

    \var_dump(\get_class($slice));

    $slice = yield $connection->readStreamEventsBackwardAsync(
        'opium2-bar',
        10,
        2,
        true
    );

    \var_dump(\get_class($slice));

    $event = yield $connection->readEventAsync('opium2-bar', 2, true);

    \var_dump(\get_class($event));

    $m = yield $connection->getStreamMetadataAsync('opium2-bar');

    \var_dump(\get_class($m));

    $r = yield $connection->setStreamMetadataAsync('opium2-bar', ExpectedVersion::Any, new StreamMetadata(
        null, null, null, null, null, [
            'foo' => 'bar',
        ]
    ));

    \var_dump(\get_class($r));

    $m = yield $connection->getStreamMetadataAsync('opium2-bar');

    \var_dump(\get_class($m));

    $wr = yield $connection->appendToStreamAsync('opium2-bar', ExpectedVersion::Any, [
        new EventData(EventId::generate(), 'test-type', false, 'jfkhksdfhsds', 'meta'),
        new EventData(EventId::generate(), 'test-type2', false, 'kldjfls', 'meta'),
        new EventData(EventId::generate(), 'test-type3', false, 'aaa', 'meta'),
        new EventData(EventId::generate(), 'test-type4', false, 'bbb', 'meta'),
    ]);

    \var_dump(\get_class($wr));

    $ae = yield $connection->readAllEventsForwardAsync(Position::start(), 2, false, new UserCredentials(
        'admin',
        'changeit'
    ));

    \var_dump(\get_class($ae));

    $aeb = yield $connection->readAllEventsBackwardAsync(Position::end(), 2, false, new UserCredentials(
        'admin',
        'changeit'
    ));

    \var_dump(\get_class($aeb));

    $connection->close();
});
