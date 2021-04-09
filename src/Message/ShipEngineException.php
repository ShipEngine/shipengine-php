<?php

declare(strict_types=1);

namespace ShipEngine\Message;

use ShipEngine\Util;

/**
 * Error-level message - an error thrown by the ShipEngine SDK.
 * All other SDK errors inherit from this class.
 *
 * Is throwable.
 * @package ShipEngine\Message
 * @param string $message
 * @param string|null $request_id
 * @param string|null $source
 * @param string|null $type
 * @param string|null $error_code
 * @param string|null $url
 */
class ShipEngineException extends \RuntimeException implements \JsonSerializable
{
    use Util\Getters;

    /**
     * @var string|null
     */
    private ?string $request_id;

    /**
     * @var string|null
     */
    private ?string $error_code;

    /**
     * @var string|null
     */
    private ?string $source;

    /**
     * @var string|null
     */
    private ?string $type;

    /**
     * @var string|null
     */
    private ?string $url;

    /**
     * ShipEngineException constructor.
     * @param string $message
     * @param string|null $request_id
     * @param string|null $source
     * @param string|null $type
     * @param string|null $error_code
     * @param string|null $url
     */
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

    /**
     * @return array
     */
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
