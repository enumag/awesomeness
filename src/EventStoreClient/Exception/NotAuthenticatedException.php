<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient\Exception;

class NotAuthenticatedException extends \RuntimeException implements Exception
{
    public function __construct(string $message = 'Not authenticated')
    {
        parent::__construct($message);
    }
}