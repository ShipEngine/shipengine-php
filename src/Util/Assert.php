<?php declare(strict_types=1);

namespace ShipEngine\Util;

final class Assert
{
    /**
     * Asserts that the given value is a string, including an empty or whitespace string.
     *
     * @param string $expected
     * @param string $actual
     */
    public function isString(string $expected, string $actual)
    {
        if (!is_string($actual)) {
            throw new InvalidFieldValueException(
                $expected,
                'must be a string.',
                $actual
            );
        }
    }
}
