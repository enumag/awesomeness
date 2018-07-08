<?php

declare(strict_types=1);

namespace Prooph\EventStore;

use Prooph\EventStore\Data\UserCredentials;
use Prooph\EventStore\Data\WriteResult;

/** @internal */
interface EventStoreTransactionConnection
{
    public function startTransaction(
        string $stream,
        int $expectedVersion,
        UserCredentials $userCredentials = null
    ): EventStoreTransaction;

    public function continueTransaction(
        int $transactionId,
        UserCredentials $userCredentials = null
    ): EventStoreTransaction;

    public function transactionalWrite(
        EventStoreTransaction $transaction,
        array $events,
        UserCredentials $userCredentials = null
    ): void;

    public function commitTransaction(
        EventStoreTransaction $transaction,
        UserCredentials $userCredentials = null
    ): WriteResult;
}
