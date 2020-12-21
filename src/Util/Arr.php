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

    /**
     * Flatten a multi-dimensional array into a single dimension.
     */
    public static function flatten($arry = null): array
    {
        $result = array();

        if (!is_array($array)) {
            $array = func_get_args();
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, array_flatten($value));
            } else {
                $result = array_merge($result, array($key => $value));
            }
        }

        return $result;
    }
}
