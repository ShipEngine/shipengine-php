<?php

declare(strict_types=1);

namespace ShipEngine\Message;

use ShipEngine\Util;

/**
 * Error-level message.
 *
 * Is throwable.
 * @package ShipEngine\Message
 */
class ShipEngineError extends \RuntimeException implements \JsonSerializable
{
    use Util\Getters;

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

//        return parent::__construct($error_message);  // TODO: confirm if this line is idiomatic in PHP.
    }

    public function errorData()
    {
        return $this->jsonSerialize();
    }

    public function jsonSerialize()
    {
        return [
            'request_id' => $this->request_id,
            'error_source' => $this->error_source,
            'error_type' => $this->error_type,
            'error_code' => $this->error_code,
            'error_message' => $this->error_message
        ];
    }
}
