<?php

declare(strict_types=1);

namespace Prooph\PdoEventStore\UserManagement\ClientOperations;

use PDO;
use Prooph\EventStore\Data\EventData;
use Prooph\EventStore\Data\EventId;
use Prooph\EventStore\Data\ExpectedVersion;
use Prooph\EventStore\Data\SliceReadStatus;
use Prooph\EventStore\Data\UserCredentials;
use Prooph\EventStore\Exception\AccessDenied;
use Prooph\EventStore\Exception\UserNotFound;
use Prooph\EventStore\UserManagement\UserManagement;
use Prooph\PdoEventStore\PdoEventStoreSyncSyncConnection;

/** @internal */
class ChangePasswordOperation
{
    public function __invoke(
        PdoEventStoreSyncSyncConnection $eventStoreConnection,
        PDO $connection,
        string $login,
        string $oldPassword,
        string $newPassword,
        ?UserCredentials $userCredentials
    ): void {
        try {
            $streamEventsSlice = $eventStoreConnection->readStreamEventsBackward(
                UserManagement::UserStreamPrefix . $login,
                PHP_INT_MAX,
                1,
                true,
                $userCredentials
            );
        } catch (AccessDenied $e) {
            throw AccessDenied::toUserManagementOperation();
        }

        if (! $streamEventsSlice->status()->equals(SliceReadStatus::success())) {
            throw UserNotFound::withLogin($login);
        }

        $data = \json_decode($streamEventsSlice->events()[0]->data(), true);

        if (! \password_verify($oldPassword, $data['hash'])) {
            throw AccessDenied::toUserManagementOperation();
        }

        $passwordHash = \password_hash($newPassword, PASSWORD_DEFAULT);
        $data['hash'] = $passwordHash;

        $connection->beginTransaction();

        try {
            $eventStoreConnection->appendToStream(
                UserManagement::UserStreamPrefix . $login,
                ExpectedVersion::Any,
                [
                    new EventData(
                        EventId::generate(),
                        UserManagement::UserUpdated,
                        true,
                        \json_encode($data),
                        ''
                    ),
                ],
                $userCredentials
            );

            $sql = 'UPDATE users SET password_hash = ? WHERE username = ?';
            $statement = $connection->prepare($sql);
            $statement->execute([$passwordHash, $login]);
        } catch (AccessDenied $e) {
            $connection->rollBack();

            throw AccessDenied::toUserManagementOperation();
        } catch (\Exception $e) {
            $connection->rollBack();

            throw $e;
        }

        $connection->commit();
    }
}
