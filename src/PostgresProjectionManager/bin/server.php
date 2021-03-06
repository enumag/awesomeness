<?php

declare(strict_types=1);

namespace Prooph\PostgresProjectionManager;

require __DIR__ . '/../../../vendor/autoload.php';

use Amp\ByteStream\ResourceOutputStream;
use Amp\Http\Server\Server;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\Loop;
use Amp\Socket;
use DateTimeZone;
use Monolog\Logger;
use Prooph\PostgresProjectionManager\Http\RouterBuilder;
use Psr\Log\LogLevel;
use ReflectionClass;
use Zend\Console\Exception\RuntimeException as GetOptException;
use Zend\Console\Getopt;
use const SIGINT;
use const SIGTERM;

try {
    $opts = new Getopt([
        'loglevel|l-s' => 'the log level, defaults to INFO',
        'port|p=i' => 'the port to use, defaults to 2113',
        'dbhost|h=s' => 'the db host to use, defaults to localhost',
        'dbuser|u=s' => 'the db username to use, defaults to postgres',
        'dbpass=s' => 'the db password to use, defaults to postgres',
        'dbname|d=s' => 'the db name to use, defaults to event_store',
    ]);
    $opts->parse();
} catch (GetOptException $e) {
    echo $e->getUsageMessage();
    exit;
}

$logLevel = \strtolower($opts->getOption('loglevel')) ?? 'info';
$port = $opts->getOption('port') ?? 2113;
$dsn = \sprintf(
    'host=%s port=%d dbname=%s user=%s password=%s',
    $opts->getOption('dbhost') ?? 'localhost',
    $opts->getOption('dbport') ?? 5432,
    $opts->getOption('dbname') ?? 'event_store',
    $opts->getOption('dbuser') ?? 'postgres',
    $opts->getOption('dbpass') ?? 'postgres'
);

$r = new ReflectionClass(LogLevel::class);
$logLevels = $r->getConstants();

if (! \in_array($logLevel, $logLevels, true)) {
    echo 'Invalid log level "' . $logLevel . '" provided, use one of ' . \implode(', ', $logLevels);
    exit;
}

Logger::setTimezone(new DateTimeZone('UTC'));

Loop::run(function () use ($logLevel, $port, $dsn) {
    // start projection manager
    $logHandler = new StreamHandler(new ResourceOutputStream(\STDOUT), $logLevel);
    $logHandler->setFormatter(new ConsoleFormatter());

    $logger = new Logger('PROJECTIONS');
    $logger->pushHandler($logHandler);

    $projectionManager = new ProjectionManager($dsn, $logger, $logLevel);
    yield $projectionManager->start();

    // start http server
    $servers = [
        Socket\listen('0.0.0.0:' . $port),
        Socket\listen('[::]:' . $port),
    ];

    $logHandler = new StreamHandler(new ResourceOutputStream(\STDOUT), $logLevel);
    $logHandler->setFormatter(new ConsoleFormatter());

    $logger = new Logger('HTTP');
    $logger->pushHandler($logHandler);

    $router = (new RouterBuilder())($projectionManager);

    $server = new Server($servers, $router, $logger);
    yield $server->start();

    $shutdown = function (string $watcherId) use ($server, $projectionManager, $logger) {
        $logger->info('Received SIGINT or SIGTERM - shutting down');
        yield $server->stop();
        yield $projectionManager->stop();
    };

    // Stop the server when SIGINT or SIGTERM is received
    Loop::unreference(Loop::onSignal(SIGINT, $shutdown));
    Loop::unreference(Loop::onSignal(SIGTERM, $shutdown));
});
