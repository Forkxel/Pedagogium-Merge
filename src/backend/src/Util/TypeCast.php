<?php
namespace App\Utils;

class TypeCast
{
    /**
     * Převede hodnotu na string
     *
     * @param mixed $value
     * @return string
     */
    public static function toString(mixed $value): string
    {
        return (string) $value;
    }

    /**
     * Převede hodnotu na int
     *
     * @param mixed $value
     * @return int
     */
    public static function toInt(mixed $value): int
    {
        return (int) $value;
    }
}
