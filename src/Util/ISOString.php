<?php declare(strict_types=1);

namespace ShipEngine\Util;

final class ISOString
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

    public function hasTime(): bool
    {
        return preg_match('/[0-9]*T[0-9]*/', $this->value) == 1;
    }

    public function hasTimezone(): bool
    {
        return $this->hasTime() && preg_match('/(?<=T).*[+-][0-9]|Z$/', $this->value) == 1;
    }
}
