<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient;

final class NamedConsumerStrategy
{
    // Distributes events to a single client until it is full. Then round robin to the next client.
    public const DispatchToSingle = 'DispatchToSingle';

    // Distribute events to each client in a round robin fashion.
    public const RoundRobin = 'RoundRobin';

    // Distribute events of the same streamId to the same client until it disconnects on a best efforts basis.
    // Designed to be used with indexes such as the category projection.
    public const Pinned = 'Pinned';

    public const OPTIONS = [
        'DispatchToSingle' => 'DispatchToSingle',
        'RoundRobin' => 'RoundRobin',
        'Pinned' => 'Pinned',
    ];

    private $name;
    private $value;

    private function __construct(string $name)
    {
        $this->name = $name;
        $this->value = self::OPTIONS[$name];
    }

    public static function dispatchToSingle(): self
    {
        return new self('DispatchToSingle');
    }

    public static function roundRobin(): self
    {
        return new self('RoundRobin');
    }

    public static function pinned(): self
    {
        return new self('Pinned');
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

    public function equals(NamedConsumerStrategy $other): bool
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
