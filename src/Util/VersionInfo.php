<?php declare(strict_types=1);

namespace ShipEngine\Util;

/**
 * Class VersionInfo
 * @package ShipEngine\Util
 */
final class VersionInfo
{
    /**
     * TODO: add docs
     */
    const MAJOR = 0;
    /**
     *
     */
    const MINOR = 0;
    /**
     *
     */
    const PATCH = 1;

    /**
     * @return string
     */
    public static function string()
    {
        return implode('.', array(self::MAJOR, self::MINOR, self::PATCH));
    }
}
