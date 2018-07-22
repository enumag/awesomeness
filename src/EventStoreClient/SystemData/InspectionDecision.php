<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient\SystemData;

/** @internal */
final class InspectionDecision
{
    public const OPTIONS = [
        'DoNothing' => 0,
        'EndOperation' => 1,
        'Retry' => 2,
        'Reconnect' => 3,
        'Subscribed' => 4,
    ];

    public const DoNothing = 0;
    public const EndOperation = 1;
    public const Retry = 2;
    public const Reconnect = 3;
    public const Subscribed = 4;

    private $name;
    private $value;

    private function __construct(string $name)
    {
        $this->name = $name;
        $this->value = self::OPTIONS[$name];
    }

    public static function doNothing(): self
    {
        return new self('DoNothing');
    }

    public static function endOperation(): self
    {
        return new self('EndOperation');
    }

    public static function retry(): self
    {
        return new self('Retry');
    }

    public static function reconnect(): self
    {
        return new self('Reconnect');
    }

    public static function subscribed(): self
    {
        return new self('Subscribed');
    }

    public static function fromName(string $value): self
    {
        if (! isset(self::OPTIONS[$value])) {
            throw new \InvalidArgumentException('Unknown enum name given');
        }

        return self::{$value}();
    }

    public static function fromValue($value): self
    {
        foreach (self::OPTIONS as $name => $v) {
            if ($v === $value) {
                return self::{$name}();
            }
        }

        throw new \InvalidArgumentException('Unknown enum value given');
    }

    public function equals(InspectionDecision $other): bool
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
