<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient;

use Amp\Promise;
use Prooph\EventStoreClient\Internal\EventStoreAsyncTransactionConnection;

class EventStoreAsyncTransaction
{
    /** @var int */
    private $transactionId;
    /** @var UserCredentials|null */
    private $userCredentials;
    /** @var EventStoreAsyncTransactionConnection */
    private $connection;
    /** @var bool */
    private $isRolledBack;
    /** @var bool */
    private $isCommitted;

    public function __construct(
        int $transactionId,
        ?UserCredentials $userCredentials,
        EventStoreAsyncTransactionConnection $connection
    ) {
        $this->transactionId = $transactionId;
        $this->userCredentials = $userCredentials;
        $this->connection = $connection;
    }

    public function transactionId(): int
    {
        return $this->transactionId;
    }

    /** @return Promise<WriteResult> */
    public function commit(): Promise
    {
        if ($this->isRolledBack) {
            throw new \RuntimeException('Cannot commit a rolledback transaction');
        }

        if ($this->isCommitted) {
            throw new \RuntimeException('Transaction is already committed');
        }

        return $this->connection->commitTransactionAsync($this, $this->userCredentials);
    }

    /**
     * @param EventData[] $events
     *
     * @return Promise<void>
     */
    public function writeAsync(array $events): Promise
    {
        if ($this->isRolledBack) {
            throw new \RuntimeException('Cannot commit a rolledback transaction');
        }

        if ($this->isCommitted) {
            throw new \RuntimeException('Transaction is already committed');
        }

        return $this->connection->transactionalWriteAsync($this, $events, $this->userCredentials);
    }

    public function rollback(): void
    {
        if ($this->isCommitted) {
            throw new \RuntimeException('Transaction is already committed');
        }

        $this->isRolledBack = true;
    }
}
