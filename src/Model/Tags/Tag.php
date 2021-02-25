<?php


namespace ShipEngine\Model\Tags;

use ShipEngine\Util;

/**
 * Tag created by the 'create_tag' remote procedure.
 *
 * @property string $name
 * */
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

    /**
     * Get Tag name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}