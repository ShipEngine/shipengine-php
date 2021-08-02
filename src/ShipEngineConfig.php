<?php declare(strict_types=1);

namespace ShipEngine;

use DateInterval;
use ShipEngine\Message\ValidationException;
use ShipEngine\Util\Assert;
use ShipEngine\Util\Constants\Endpoints;

/**
 * Class ShipEngineConfig - This is the configuration object for the ShipEngine object and it's properties are
 * used throughout this SDK.
 *
 * @package ShipEngine
 */
final class ShipEngineConfig implements \JsonSerializable
{
    /**
     * The default base uri for the ShipEngineClient.
     */
    public const DEFAULT_BASE_URI = Endpoints::SHIPENGINE_REST_URL;

    /**
     * Default page size for responses from ShipEngine API.
     */
    public const DEFAULT_PAGE_SIZE = 50;

    /**
     * Default number of retries the ShipEngineClient should make before returning an exception.
     */
    public const DEFAULT_RETRIES = 1;

    /**
     * Default timeout for the ShipEngineClient in seconds as a **DateInterval**.
     */
    public const DEFAULT_TIMEOUT = 'PT10S';


    /**
     * A ShipEngine API Key, sandbox API Keys start with **TEST_**.
     *
     * @var string
     */
    public string $apiKey;

    /**
     * The configured base uri for the ShipEngineClient.
     *
     * @var string
     */
    public string $baseUrl;

    /**
     * Configured page size for responses from ShipEngine API.
     *
     * @var int
     */
    public int $pageSize;

    /**
     * Configured number of retries the ShipEngineClient should make before returning an exception.
     *
     * @var int
     */
    public int $retries;

    /**
     * Configured timeout for the ShipEngineClient in seconds as a **DateInterval**.
     *
     * @var DateInterval
     */
    public DateInterval $timeout;

    /**
     * ShipEngineConfig constructor.
     *
     * @param array $config {apiKey:string, baseUrl:string, pageSize:int,
     * retries:int, timeout:DateInterval}
     */
    public function __construct(array $config = array())
    {
        $assert = new Assert();
        $assert->isApiKeyValid($config);
        $this->apiKey = $config['apiKey'];


        if (isset($config['retries']) === true && $config['retries'] >= 0) {
            $this->retries = $config['retries'];
        } elseif (isset($config['retries']) === false) {
            $this->retries = self::DEFAULT_RETRIES;
        } elseif ($config['retries'] < 0) {
            throw new ValidationException(
                'Retries must be zero or greater.',
                null,
                'shipengine',
                'validation',
                'invalid_field_value'
            );
        }

        $timeout = $config['timeout'];
        if ($timeout instanceof DateInterval) {
            $assert->isTimeoutValid($timeout);
            $this->timeout = $timeout;
        } elseif (isset($config['timeout']) === false) {
            $this->timeout = new DateInterval(self::DEFAULT_TIMEOUT);
        } else {
            throw new ValidationException(
                'Timeout is not a DateInterval.',
                null,
                'shipengine',
                'validation',
                'invalid_field_value'
            );
        }

        $this->baseUrl = $config['baseUrl'] ?? self::DEFAULT_BASE_URI;
        $this->pageSize = $config['pageSize'] ?? self::DEFAULT_PAGE_SIZE;
    }

    /**
     * Merge in method level config into the global config used by the **ShipEngine** object.
     *
     * @param array|null $newConfig
     * @return $this
     */
    public function merge(?array $newConfig = null): ShipEngineConfig
    {
        if (!isset($newConfig)) {
            return $this;
        }

        $config = array();

        isset($newConfig['apiKey']) ?
            ($config['apiKey'] = $newConfig['apiKey']) :
            ($config['apiKey'] = $this->apiKey);

        isset($newConfig['baseUrl']) ?
            ($config['baseUrl'] = $newConfig['baseUrl']) :
            ($config['baseUrl'] = $this->baseUrl);

        isset($newConfig['pageSize']) ?
            ($config['pageSize'] = $newConfig['pageSize']) :
            ($config['pageSize'] = $this->pageSize);

        isset($newConfig['retries']) ?
            ($config['retries'] = $newConfig['retries']) :
            ($config['retries'] = $this->retries);

        isset($newConfig['timeout']) ?
            ($config['timeout'] = $newConfig['timeout']) :
            ($config['timeout'] = $this->timeout);

        return new ShipEngineConfig($config);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        return [
          'apiKey' => $this->apiKey,
          'baseUrl' => $this->baseUrl,
          'pageSize' => $this->pageSize,
          'retries' => $this->retries,
          'timeout' => $this->timeout->s
        ];
    }
}
