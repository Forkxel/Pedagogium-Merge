<?php
namespace App\Utils;

/**
 * Převod na pevný typ
 *
 * @param mixed $value
 * @param 'string'|'int' $type
 * @param string|int $default
 * @return string|int
 */
function cast(mixed $value, string $type, mixed $default): string|int
{
    return match($type) {
        'string' => (string) $value,
        'int' => (int) $value,
    };
}
