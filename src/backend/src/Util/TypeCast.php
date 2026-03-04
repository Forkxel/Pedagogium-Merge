<?php
namespace App\Utils;

class TypeCast
{
    /**
     * Přetypuje cokoliv na string.
     * Pokud je to pole nebo objekt bez __toString(), vrátí prázdný string.
     *
     * @param mixed $value
     * @return string
     */
    public static function toString(mixed $value): string
    {
        if (is_scalar($value) || $value === null) {
            return (string) $value;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        if (is_array($value)) {
            // Můžeme vrátit JSON reprezentaci pole
            return json_encode($value, JSON_THROW_ON_ERROR);
        }

        return '';
    }

    /**
     * Přetypuje cokoliv na int.
     * Pokud je to pole nebo objekt, vrátí 0.
     *
     * @param mixed $value
     * @return int
     */
    public static function toInt(mixed $value): int
    {
        if (is_scalar($value)) {
            return (int) $value;
        }

        return 0;
    }

    /**
     * Přetypuje cokoliv na array.
     * Pokud je to objekt implementující Traversable, převede na array.
     * Pokud je to scalar, vrátí array s jednou hodnotou.
     * Jinak vrátí prázdné pole.
     *
     * @param mixed $value
     * @return array
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
