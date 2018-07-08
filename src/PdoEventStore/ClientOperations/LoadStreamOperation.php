<?php

declare(strict_types=1);

namespace Prooph\PdoEventStore\ClientOperations;

use PDO;
use Prooph\EventStore\Common\SystemRoles;
use Prooph\EventStore\Common\SystemStreams;
use Prooph\EventStore\Data\SystemSettings;
use Prooph\EventStore\Exception\AccessDenied;
use Prooph\EventStore\Exception\RuntimeException;
use Prooph\EventStore\Exception\StreamDeleted;
use Prooph\PdoEventStore\Internal\LoadStreamResult;
use Prooph\PdoEventStore\Internal\StreamOperation;

/** @internal */
class LoadStreamOperation
{
    public function __invoke(
        PDO $connection,
        string $stream,
        int $operation,
        SystemSettings $systemSettings,
        array $userRoles
    ): LoadStreamResult {
        switch ($connection->getAttribute(PDO::ATTR_DRIVER_NAME)) {
            case 'mysql':
                $concat = "GROUP_CONCAT(stream_acl.role SEPARATOR ',')";
                break;
            case 'pgsql':
                $concat = "STRING_AGG(stream_acl.role, ',')";
                break;
            default:
                throw new RuntimeException('Invalid PDO driver used');
        }

        $statement = $connection->prepare(<<<SQL
SELECT streams.mark_deleted, streams.deleted, $concat as stream_roles
    FROM streams
    LEFT JOIN stream_acl ON streams.stream_name = stream_acl.stream_name AND stream_acl.operation = ?
    WHERE streams.stream_name = ?
    GROUP BY streams.stream_name, streams.mark_deleted, streams.deleted
    LIMIT 1;
SQL
        );
        $statement->execute([$operation, $stream]);
        $statement->setFetchMode(PDO::FETCH_OBJ);
        $data = $statement->fetch();
        if (false === $data) {
            if (! SystemStreams::isSystemStream($stream)) {
                switch ($operation) {
                    case StreamOperation::Read:
                        $toCheck = $systemSettings->userStreamAcl()->readRoles();
                        break;
                    case StreamOperation::Write:
                        $toCheck = $systemSettings->userStreamAcl()->writeRoles();
                        break;
                    case StreamOperation::Delete:
                        $toCheck = $systemSettings->userStreamAcl()->deleteRoles();
                        break;
                }
            } else {
                switch ($operation) {
                    case StreamOperation::Read:
                        $toCheck = $systemSettings->systemStreamAcl()->readRoles();
                        break;

                    case StreamOperation::Write:
                        $toCheck = $systemSettings->systemStreamAcl()->writeRoles();
                        break;

                    case StreamOperation::Delete:
                        $toCheck = $systemSettings->systemStreamAcl()->deleteRoles();
                        break;

                    case StreamOperation::MetaRead:
                        $toCheck = $systemSettings->systemStreamAcl()->metaReadRoles();
                        break;

                    case StreamOperation::MetaWrite:
                        $toCheck = $systemSettings->systemStreamAcl()->metaWriteRoles();
                        break;
                }
            }
        } else {
            if ($data->mark_deleted || $data->deleted) {
                throw StreamDeleted::with($stream);
            }

            $toCheck = [SystemRoles::All];
            if (\is_string($data->stream_roles)) {
                $toCheck = \explode(',', $data->stream_roles);
            }
        }

        if (empty(\array_intersect($userRoles, $toCheck))) {
            throw AccessDenied::toStream($stream);
        }

        if (false === $data) {
            return new LoadStreamResult(false);
        }

        return new LoadStreamResult(true);
    }
}
