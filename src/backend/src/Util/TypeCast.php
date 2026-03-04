<?php
namespace App\Utils;

public static function toString(mixed $value): string
{
    /** @phpstan-ignore-next-line */
    return (string) $value;
}

public static function toInt(mixed $value): int
{
    /** @phpstan-ignore-next-line */
    return (int) $value;
}
