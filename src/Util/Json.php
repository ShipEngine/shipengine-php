<?php declare(strict_types=1);

namespace ShipEngine\Util;

/**
 * Helper functions for JSON processing.
 */
final class Json
{
    /**
     * Take any JsonSerializable object and an array of $keys to swap out.
     */
    private static function jsonize(\JsonSerializable $obj, array $keys)
    {
        $json = $obj->jsonSerialize();

        foreach ($keys as $key) {
            $old = $key[0];
            $new = $key[1];
            $json[$new] = $json[$old];
            unset($json[$old]);
        }

        return $json;
    }
    
    /**
     * Encode a JsonSerializable object, swapping out the $keys in process.
     */
    public static function encode(\JsonSerializable $obj, array ...$keys): string
    {
        $json = self::jsonize($obj, $keys);
        return json_encode($json);
    }

    /**
     * Encode an array of JsonSerializable objects, swapping out the $keys in the process.
     */
    public static function encodeArray(array $objs, array ...$keys): string
    {
        $new = array();
        
        foreach ($objs as $obj) {
            $new[] = self::jsonize($obj, $keys);
        }
        
        return json_encode($new);
    }
}
