<?php

declare(strict_types=1);

namespace Prooph\EventStore\Data;

use Prooph\EventStore\Exception\InvalidArgumentException;

/**
 * Transaction File Position
 */
class Position
{
    /** @var int */
    private $commitPosition;
    /** @var int */
    private $preparePosition;

    /** @internal */
    public function __construct(int $commitPosition, int $preparePosition)
    {
        $this->commitPosition = $commitPosition;
        $this->preparePosition = $preparePosition;
    }

    public static function invalid(): Position
    {
        return new Position(-1, -1);
    }

    public static function headOfTf(): Position
    {
        return new Position(-1, -1);
    }

    public static function start(): Position
    {
        return new Position(0, 0);
    }

    public static function end(): Position
    {
        return new Position(-1, -1);
    }

    public static function parse(string $string): Position
    {
        if (\strlen($string) !== 32) {
            throw new InvalidArgumentException('string too short');
        }

        $commitPosition = \hexdec(\substr($string, 0, 16));
        $preparePosition = \hexdec(\substr($string, 16, 16));

        return new Position($commitPosition, $preparePosition);
    }

    public function commitPosition(): int
    {
        return $this->commitPosition;
    }

    public function preparePosition(): int
    {
        return $this->preparePosition;
    }

    public function asString(): string
    {
        return \substr('000000000000000' . \dechex($this->commitPosition), -16)
            . \substr('000000000000000' . \dechex($this->preparePosition), -16);
    }

    public function toString(): string
    {
        return 'C:' . $this->commitPosition . '/P:' . $this->preparePosition;
    }

    public function equals(Position $other): bool
    {
        return $this->commitPosition === $other->commitPosition && $this->preparePosition === $other->preparePosition;
    }

    public function greater(Position $other): bool
    {
        return $this->commitPosition > $other->commitPosition
            || ($this->commitPosition === $other->commitPosition && $this->preparePosition > $other->preparePosition);
    }

    public function smaller(Position $other): bool
    {
        return $this->commitPosition < $other->commitPosition
            || ($this->commitPosition === $other->commitPosition && $this->preparePosition < $other->preparePosition);
    }

    public function greaterOrEquals(Position $other): bool
    {
        return $this->greater($other) || $this->equals($other);
    }

    public function smallerOrEquals(Position $other): bool
    {
        return $this->smaller($other) || $this->equals($other);
    }
}
