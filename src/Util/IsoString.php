<?php declare(strict_types=1);

namespace ShipEngine\Util;

/**
 * A string representing a Date, DateTime, or DateTime with Timezone.
 */
final class IsoString
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
    
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Returns whether or not the `IsoString` includes a time element.
     */
    public function hasTime(): bool
    {
        return preg_match('/[0-9]*T[0-9]*/', $this->value) == 1;
    }

    /**
     * Returns whether or not the `IsoString` includes a timezone element.
     */
    public function hasTimezone(): bool
    {
        return $this->hasTime() && preg_match('/(?<=T).*[+-][0-9]|Z$/', $this->value) == 1;
    }
}
