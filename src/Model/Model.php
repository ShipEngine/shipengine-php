<?php declare(strict_types=1);

namespace ShipEngine\Model;

/**
 *
 */
trait Model
{
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
}
