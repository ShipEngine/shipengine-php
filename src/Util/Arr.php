<?php declare(strict_types=1);

namespace ShipEngine\Util\Arr {
    function sub_array(array $old, ...$keys): array
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
