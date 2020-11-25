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

    public function __construct(AddressQuery $original, ?Address $normalized = null, array $exceptions = array())
    {
        $this->original = $original;
        $this->normalized = $normalized;
        $this->exceptions = $exceptions;
    }
    
    public function info(): array
    {
        return array();
    }

    public function warnings(): array
    {
        return array();
    }

    public function errors(): array
    {
        return array();
    }
}
