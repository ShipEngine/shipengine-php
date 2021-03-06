<?php declare(strict_types=1);

namespace ShipEngine\Model\Tag;

use ShipEngine\Util;

/**
 * Tag created by the 'tag/create' remote procedure.
 *
 * @property string $name
 */
final class Tag
{
    use Util\Getters;

    private string $name;

    /**
     * Tag constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
