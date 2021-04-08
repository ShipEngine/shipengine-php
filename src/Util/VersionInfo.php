<?php declare(strict_types=1);

namespace ShipEngine\Util;

/**
 * Class VersionInfo, this class provides constants used for Version Management.
 *
 * @package ShipEngine\Util
 */
final class VersionInfo
{
    /**
     * @const MAJOR
     */
    const MAJOR = 0;

    /**
     * @const MINOR
     */
    const MINOR = 0;

    /**
     * @const PATCH
     */
    const PATCH = 1;

    /**
     * @return string
     */
    public static function string(): string
    {
        return implode('.', array(self::MAJOR, self::MINOR, self::PATCH));
    }
}
