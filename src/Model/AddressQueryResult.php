<?php declare(strict_types=1);

namespace ShipEngine\Model;

use ShipEngine\Exception\ErrorException;
use ShipEngine\Exception\InfoException;
use ShipEngine\Exception\WarningException;

/**
 * The result of an \ShipEngine\Service\AddressesService::query() on a \ShipEngine\Model\AddressQuery.
 *
 * @property \ShipEngine\Model\AddressQuery $original
 * @property ?\ShipEngine\Model\Address $normalized
 */
final class AddressQueryResult
{
    use Getters;
    
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
     * Get all \ShipEngine\Exception\ShipEngineException exceptions by type.
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
     * Get all \ShipEngine\Exception\InfoException exceptions.
     */
    public function info(): array
    {
        return $this->exceptionsByType(InfoException::class);
    }

    /**
     * Get all \ShipEngine\Exception\WarningException exceptions.
     */
    public function warnings(): array
    {
        return $this->exceptionsByType(WarningException::class);
    }

    /**
     * Get all \ShipEngine\Exception\ErrorException exceptions.
     */
    public function errors(): array
    {
        return $this->exceptionsByType(ErrorException::class);
    }
}
