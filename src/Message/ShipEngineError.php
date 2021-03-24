<?php

declare(strict_types=1);

namespace ShipEngine\Message;

/**
 * Error-level message.
 *
 * Is throwable.
 * @package ShipEngine\Message
 */
class ShipEngineError extends \RuntimeException
{
    private string $error_message;

    private ?string $request_id;

    private ?string $error_source;

    private ?string $error_type;

    private ?string $error_code;

    public function __construct(
        string $error_message,
        ?string $request_id = null,
        ?string $error_source = null,
        ?string $error_type = null,
        ?string $error_code = null
    ) {
        $this->request_id = $request_id;
        $this->error_source = $error_source;
        $this->error_type = $error_type;
        $this->error_code = $error_code;
        $this->error_message = $error_message;
    }
}
