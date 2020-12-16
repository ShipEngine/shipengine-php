<?php declare(strict_types=1);

namespace ShipEngine\Model\Address;

use ShipEngine\Exception\ErrorException;
use ShipEngine\Exception\InfoException;
use ShipEngine\Exception\WarningException;
use ShipEngine\Util;

/**
 * The result of an \ShipEngine\Services\AddressesService::query() on a \ShipEngine\Model\Address\Query.
 *
 * @property \ShipEngine\Model\Address\Query $query
 * @property ?\ShipEngine\Model\Address\Address $normalized
 */
final class QueryResult
{
    use Util\Getters;
    
    private Query $query;
    private ?Address $normalized;
    private array $exceptions;

    public function __construct(Query $query, ?Address $normalized = null, array $exceptions = array())
    {
        $this->query = $query;
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
