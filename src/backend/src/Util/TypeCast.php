<?php
namespace App\Utils;

class TypeCast
{
    public static function toString(mixed $value): string
    {
        if (is_scalar($value) || $value === null) {
            return (string) $value;
        }

        // fallback pro objekty s __toString()
        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        // pro pole a jiné typy vrátíme prázdný string
        return '';
    }

    public static function toInt(mixed $value): int
    {
        if (is_scalar($value)) {
            return (int) $value;
        }

        return 0;
    }
}
