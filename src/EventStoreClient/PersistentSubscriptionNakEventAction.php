<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient;

class PersistentSubscriptionNakEventAction
{
    public const OPTIONS = [
        'Unknown' => 0,
        'Park' => 1,
        'Retry' => 2,
        'Skip' => 3,
        'Stop' => 4,
    ];

    // Client unknown on action. Let server decide
    public const Unknown = 0;
    // Park message do not resend. Put on poison queue
    public const Park = 1;
    // Explicitly retry the message
    public const Retry = 2;
    // Skip this message do not resend do not put in poison queue
    public const Skip = 3;
    // Stop the subscription
    public const Stop = 4;

    private $name;
    private $value;

    private function __construct(string $name)
    {
        $this->name = $name;
        $this->value = self::OPTIONS[$name];
    }

    public static function unknown(): self
    {
        return new self('Unknown');
    }

    public static function park(): self
    {
        return new self('Park');
    }

    public static function retry(): self
    {
        return new self('Retry');
    }

    public static function skip(): self
    {
        return new self('Skip');
    }

    public static function stop(): self
    {
        return new self('Stop');
    }

    public static function byName(string $value): self
    {
        if (! isset(self::OPTIONS[$value])) {
            throw new \InvalidArgumentException('Unknown enum name given');
        }

        return self::{$value}();
    }

    public static function byValue($value): self
    {
        foreach (self::OPTIONS as $name => $v) {
            if ($v === $value) {
                return self::{$name}();
            }
        }

        throw new \InvalidArgumentException('Unknown enum value given');
    }

    public function equals(PersistentSubscriptionNakEventAction $other): bool
    {
        return \get_class($this) === \get_class($other) && $this->name === $other->name;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function value()
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
