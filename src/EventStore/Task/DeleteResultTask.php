<?php

declare(strict_types=1);

namespace Prooph\EventStore\Task;

use Prooph\EventStore\DeleteResult;
use Prooph\EventStore\Task as BaseTask;

class DeleteResultTask extends BaseTask
{
    public function result(bool $wait = false): ?DeleteResult
    {
        if ($wait) {
            $this->promise->wait(false);
        }

        return $this->result;
    }
}
