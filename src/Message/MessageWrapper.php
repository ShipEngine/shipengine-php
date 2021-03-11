<?php declare(strict_types=1);

namespace ShipEngine\Message;

/**
 * Wraps any class that wants to track an array of \ShipEngine\Message\ShipEngineMessage
 * or \ShipEngine\Message\ShipEngineError.
 */
trait MessageWrapper
{
    /**
     * @var array
     */
    private array $messages;

    /**
     * Get all \ShipEngine\Message\Message | \ShipEngine\Message\Error by type.
     */
    private function messagesByType(string $class)
    {
        $messages = array();
        foreach ($this->messages as $message) {
            if (get_class($message) === $class) {
                $messages[] = $message;
            }
        }
        return $messages;
    }

    /**
     * Get all \ShipEngine\Message\ShipEngineInfo messages.
     */
    public function info(): array
    {
        return $this->messagesByType(ShipEngineInfo::class);
    }

    /**
     * Get all \ShipEngine\Message\ShipEngineWarning messages.
     */
    public function warnings(): array
    {
        return $this->messagesByType(ShipEngineWarning::class);
    }

    /**
     * Get all \ShipEngine\Message\ShipEngineError exceptions.
     */
    public function errors(): array
    {
        return $this->messagesByType(ShipEngineErrorMessage::class);
    }
}
