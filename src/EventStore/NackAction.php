<?php

declare(strict_types=1);

namespace Prooph\EventStore;

class NackAction
{
    public const OPTIONS = [
        'Park' => 'Park',
        'Retry' => 'Retry',
        'Skip' => 'Skip',
        'Stop' => 'Stop',
    ];

    public const Park = 'Park';
    public const Retry = 'Retry';
    public const Skip = 'Skip';
    public const Stop = 'Stop';

    private $name;
    private $value;

    private function __construct(string $name)
    {
        $this->name = $name;
        $this->value = self::OPTIONS[$name];
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

    public function equals(NackAction $other): bool
    {
        return get_class($this) === get_class($other) && $this->name === $other->name;
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