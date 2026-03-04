<?php
namespace App\Utils;

class TypeCast
{
    public static function toString(mixed $value): string
    {
        if (is_scalar($value) || $value === null) {
            return (string) $value;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        if (is_array($value)) {
            return json_encode($value, JSON_THROW_ON_ERROR);
        }

        return '';
    }

    public static function toInt(mixed $value): int
    {
        if (is_scalar($value)) {
            return (int) $value;
        }

        return 0;
    }

    /**
     * Přetypuje cokoliv na array.
     *
     * @template T
     * @param iterable<T>|T|null $value
     * @return array<T>
     */
    public static function toArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value instanceof \Traversable) {
            return iterator_to_array($value);
        }

        if ($value !== null) {
            return [$value];
        }

        return [];
    }
}
