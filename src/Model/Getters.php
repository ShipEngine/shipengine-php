<?php declare(strict_types=1);

namespace ShipEngine\Model;

/**
 * Expose getters for private properties.
 */
trait Getters
{
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        throw new \RuntimeException($property . ' does not exist.');
    }
}
