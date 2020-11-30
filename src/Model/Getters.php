<?php declare(strict_types=1);

namespace ShipEngine\Model;

/**
 *
 */
trait Getters
{
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
}
