<?php declare(strict_types=1);

namespace ShipEngine;

use DateInterval;
use ShipEngine\Message\Events\ShipEngineEventListener;
use ShipEngine\Message\ValidationException;
use ShipEngine\Util\Assert;
use ShipEngine\Util\Constants\Endpoints;

final class ShipEngineConfig implements \JsonSerializable
{
    public const DEFAULT_BASE_URI = Endpoints::SHIPENGINE_RPC_URL;
    public const DEFAULT_PAGE_SIZE = 50;
    public const DEFAULT_RETRIES = 1;
    public const DEFAULT_TIMEOUT = 'PT5S';

    public string $apiKey;
    public string $baseUrl;
    public int $pageSize;
    public int $retries;
    public DateInterval $timeout;
    public $eventListener;

    /**
     * ShipEngineConfig constructor.
     *
     * @param array $config {apiKey:string, baseUrl:string, pageSize:int,
     * retries:int, timeout:DateInterval, eventListener:object}
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

        isset($config['eventListener']) ?
            $this->eventListener = $config['eventListener'] :
            $this->eventListener =  new ShipEngineEventListener();

        $this->baseUrl = $config['baseUrl'] ?? self::DEFAULT_BASE_URI;
        $this->pageSize = $config['pageSize'] ?? self::DEFAULT_PAGE_SIZE;
    }

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

        isset($newConfig['timeout']) ?
            ($config['timeout'] = $newConfig['timeout']) :
            ($config['timeout'] = $this->timeout);

        isset($newConfig['$this->eventListener']) ?
            ($config['$this->eventListener'] = $newConfig['$this->eventListener']) :
            ($config['$this->eventListener'] = $this->eventListener);

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
          'timeout' => $this->timeout->s,
          'eventListener' => $this->eventListener
        ];
    }
}
