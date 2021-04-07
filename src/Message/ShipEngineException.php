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
class ShipEngineException extends \RuntimeException implements \JsonSerializable
{
    use Util\Getters;

    private ?string $request_id;

    private ?string $error_code;

    private ?string $source;

    private ?string $type;

    private ?string $url;

    public function __construct(
        string $message,
        ?string $request_id = null,
        ?string $source = null,
        ?string $type = null,
        ?string $error_code = null,
        ?string $url = null
    ) {
        $this->request_id = $request_id;
        $this->source = $source;
        $this->type = $type;
        $this->error_code = $error_code;
        $this->url = $url;

        parent::__construct($message);
    }

    public function jsonSerialize()
    {
        return [
            'request_id' => $this->request_id,
            'source' => $this->source,
            'type' => $this->type,
            'error_code' => $this->error_code,
            'message' => $this->message,
            'url' => $this->url
        ];
    }
}
