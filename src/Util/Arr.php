<?php declare(strict_types=1);

namespace ShipEngine\Util;

/**
 * Collects helper functions for Array presenting them as static methods.
 */
class Arr
{
    /**
     * Create a new associate array that only includes the given $keys.
     */
    public static function subArray(array $old, ...$keys): array
    {
        $new = array();
        foreach ($keys as $key) {
            if (array_key_exists($key, $old)) {
                $new[$key] = $old[$key];
            }
        }
        return $new;
    }
}
