<?php

declare(strict_types=1);

namespace Prooph\EventStore\Task;

use Prooph\EventStore\Task as BaseTask;
use Prooph\EventStore\WriteResult;

class WriteResultTask extends BaseTask
{
    public function result(bool $wait = false): ?WriteResult
    {
        if ($wait) {
            $this->promise->wait(false);
        }

        return $this->result;
    }
}
