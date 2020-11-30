<?php declare(strict_types=1);

namespace ShipEngine\Model;

use ShipEngine\Exception\ErrorException;
use ShipEngine\Exception\InfoException;
use ShipEngine\Exception\WarningException;

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

    /**
     *
     */
    private function exceptionsByType(string $class)
    {
        $exceptions = array();
        foreach ($this->exceptions as $exception) {
            if (get_class($exception) === $class) {
                $exceptions[] = $exception;
            }
        }
        return $exceptions;
    }

    /**
     *
     */
    public function info(): array
    {
        return $this->exceptionsByType(InfoException::class);
    }

    /**
     *
     */
    public function warnings(): array
    {
        return $this->exceptionsByType(WarningException::class);
    }

    /**
     *
     */
    public function errors(): array
    {
        return $this->exceptionsByType(ErrorException::class);
    }
}
