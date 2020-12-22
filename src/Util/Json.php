<?php declare(strict_types=1);

namespace ShipEngine\Util;

final class Json
{
    /**
     *
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
     *
     */
    public static function encode(\JsonSerializable $obj, array ...$keys): string
    {
        $json = self::jsonize($obj, $keys);
        return json_encode($json);
    }

    /**
     *
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
