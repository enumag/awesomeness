<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient\Exception;

class UnexpectedValueException extends \UnexpectedValueException implements EventStoreClientException
{
}
