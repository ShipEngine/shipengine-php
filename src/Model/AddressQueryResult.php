<?php declare(strict_types=1);

namespace ShipEngine\Model;

use ShipEngine\Model\Address;
use ShipEngine\Model\AddressQuery;
use ShipEngine\Model\Model;

/**
 *
 */
final class AddressQueryResult
{
    use Model;
    
    private AddressQuery $original;
    private ?Address $normalized;
    private array $exceptions;

    public function __construct(AddressQuery $original, Address $normalized = null, array $exceptions = array())
    {
        $this->original = $original;
        $this->normalized = $normalized;
        $this->exceptions = $exceptions;
    }
    
    public function isValid(): bool
    {
        if (isset($this->exceptions)) {
            return count($this->exceptions) > 0;
        }

        return true;
    }

    public function info(): array
    {
    }

    public function warnings(): array
    {
    }

    public function errors(): array
    {
    }

    public function __toString(): string
    {
    }
}
