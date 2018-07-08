<?php

declare(strict_types=1);

namespace Prooph\PdoEventStore\ClientOperations;

use PDO;
use Prooph\EventStore\Common\SystemStreams;
use Prooph\EventStore\Data\UserCredentials;

/** @internal */
class DeleteStreamOperation
{
    public function __invoke(PDO $connection, string $stream, bool $hardDelete, ?UserCredentials $userCredentials): void
    {
        if (SystemStreams::isSystemStream($stream)) {
            $statement = $connection->prepare('DELETE FROM streams WHERE stream_name = ?');
            $statement->execute([$stream]);

            $statement = $connection->prepare('DELETE FROM events WHERE stream_name = ?');
            $statement->execute([$stream]);
        } elseif ($hardDelete) {
            $statement = $connection->prepare('UPDATE streams SET mark_deleted = ?, deleted = ? WHERE stream_name = ?');
            $statement->execute([0, 1, $stream]);

            $statement = $connection->prepare('DELETE FROM events WHERE stream_name = ?);');
            $statement->execute([$stream]);
        } else {
            $statement = $connection->prepare('UPDATE streams SET mark_deleted = ? WHERE stream_name = ?');
            $statement->execute([true, $stream]);
        }
    }
}
