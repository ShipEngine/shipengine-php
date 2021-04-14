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
     * If the error came from the ShipEngine server (as opposed to a client-side error)
     * then this is the unique ID of the HTTP request that returned the error.
     * You can use this ID when contacting ShipEngine support for help.
     *
     * @var string|null
     */
    private ?string $request_id;

    /**
     * A code that indicates the specific error that occurred, such as missing a
     * required field, an invalid address, a timeout, etc.
     *
     * @var string|null
     */
    private ?string $error_code;

    /**
     * Indicates where the error originated. This lets you know whether you should
     * contact ShipEngine for support or if you should contact the carrier or
     * marketplace instead.
     *
     * @link https://www.shipengine.com/docs/errors/codes/#error-source
     * @var string|null
     */
    private ?string $source;

    /**
     * Indicates the type of error that occurred, such as a validation error, a
     * security error, etc.
     *
     * @link https://www.shipengine.com/docs/errors/codes/#error-type
     * @var string|null
     */
    private ?string $type;

    /**
     * Some errors include a URL that you can visit to learn more about the error,
     * find out how to resolve it, or get support.
     *
     * @var string|null
     */
    private ?string $url;

    /**
     * ShipEngineException constructor - Instantiates a client-side error.
     *
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
        parent::__construct($message);

        $this->request_id = $request_id;
        $this->source = isset($source) ? $source : 'shipengine';
        $this->type = $type;
        $this->error_code = $error_code;
        $this->url = isset($url) ? $url : 'https://www.shipengine.com/docs/errors/codes/';
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
