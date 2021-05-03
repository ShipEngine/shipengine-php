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
     * MAJOR version number
     */
    public const MAJOR = 0;

    /**
     * MINOR version number
     */
    public const MINOR = 0;

    /**
     * PATCH version number
     */
    public const PATCH = 1;

    /**
     * Return the version as a string.
     *
     * @return string
     */
    public static function string(): string
    {
        return implode('.', array(self::MAJOR, self::MINOR, self::PATCH));
    }
}
