<?php declare(strict_types=1);

namespace ShipEngine\Exception;

/**
 * Wraps any class that wants to track an array of \ShipEngine\Exception\ShipEngineException.
 */
abstract class Wrapper
{
    private array $exceptions;

    public function __construct(array $exceptions)
    {
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
