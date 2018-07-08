<?php

declare(strict_types=1);

namespace Prooph\EventStore\UserManagement;

use Prooph\EventStore\Data\UserCredentials;
use Prooph\EventStore\Data\UserData;

/** @internal */
interface UserManagement
{
    public const UserUpdated = '$UserUpdated';
    public const PasswordChanged = '$PasswordChanged';
    public const UserPasswordNotificationsStream = '$users-password-notifications';
    public const UsersStream = '$users';
    public const UsersStreamType = '$user';

    public const UserEventType = '$User';
    public const UserStreamPrefix = '$user-';

    public function changePassword(
        string $login,
        string $oldPassword,
        string $newPassword,
        UserCredentials $userCredentials = null
    ): void;

    /**
     * @param string $login
     * @param string $fullName
     * @param string $password
     * @param string[] $groups
     * @param UserCredentials|null $userCredentials
     * @return void
     */
    public function createUser(
        string $login,
        string $fullName,
        string $password,
        array $groups,
        UserCredentials $userCredentials = null
    ): void;

    public function deleteUser(string $login, UserCredentials $userCredentials = null): void;

    public function disableUser(string $login, UserCredentials $userCredentials = null): void;

    public function enableUser(string $login, UserCredentials $userCredentials = null): void;

    public function getUser(string $login, UserCredentials $userCredentials = null): UserData;

    /**
     * @return UserData[]
     */
    public function getAllUsers(UserCredentials $userCredentials = null): array;

    public function resetPassword(string $login, string $newPassword, UserCredentials $userCredentials = null): void;

    /**
     * @param string $login
     * @param string $fullName
     * @param string[] $groups
     * @param UserCredentials|null $userCredentials
     * @return void
     */
    public function updateUser(
        string $login,
        string $fullName,
        array $groups,
        UserCredentials $userCredentials = null
    ): void;
}
