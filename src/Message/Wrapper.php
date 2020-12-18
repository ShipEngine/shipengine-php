<?php declare(strict_types=1);

namespace ShipEngine\Message;

/**
 * Wraps any class that wants to track an array of \ShipEngine\Message\Message or \ShipEngine\Message\Error.
 */
trait Wrapper
{
    private array $messages;

    /**
     * Get all \ShipEngine\Message\Message | \ShipEngine\Message\Error by type.
     */
    private function messagesByType(string $class)
    {
        $messages = array();
        foreach ($this->messages as $message) {
            if (get_class($message) === $class) {
                $messages[] = $messages;
            }
        }
        return $messages;
    }

    /**
     * Get all \ShipEngine\Message\Info messages.
     */
    public function info(): array
    {
        return $this->messagesByType(Info::class);
    }

    /**
     * Get all \ShipEngine\Message\Warning messages.
     */
    public function warnings(): array
    {
        return $this->messagesByType(Warning::class);
    }

    /**
     * Get all \ShipEngine\Message\Error exceptions.
     */
    public function errors(): array
    {
        return $this->messagesByType(Error::class);
    }
}
