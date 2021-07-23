<?php declare(strict_types=1);

namespace ShipEngine;

use DateInterval;
use ShipEngine\Message\Events\ShipEngineEventListener;
use ShipEngine\Message\ValidationException;
use ShipEngine\Util\Assert;
use ShipEngine\Util\Constants\Endpoints;

/**
 * Class ShipEngineConfig - This is the configuration object for the ShipEngine object and it's properties are
 * used throughout this SDK>
 *
 * @package ShipEngine
 */
final class ShipEngineConfig implements \JsonSerializable
{
    /**
     * The default base uri for the ShipEngineClient.
     */
    public const DEFAULT_BASE_URI = Endpoints::SHIPENGINE_API;

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
    public const DEFAULT_TIMEOUT = 'PT5S';


    /**
     * A ShipEngine API Key, sandbox API Keys start with **TEST_**.
     *
     * @var string
     */
    public string $api_key;

    /**
     * The configured base uri for the ShipEngineClient.
     *
     * @var string
     */
    public string $base_url;

    /**
     * Configured page size for responses from ShipEngine API.
     *
     * @var int
     */
    public int $page_size;

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
     * Configured **PSR-14** event listener to consume events emitted by this SDK.
     *
     * @var object|ShipEngineEventListener
     */
    public $event_listener;

    /**
     * ShipEngineConfig constructor.
     *
     * @param array $config {api_key:string, base_url:string, page_size:int,
     * retries:int, timeout:DateInterval, event_listener:object}
     */
    public function __construct(array $config = array())
    {
        $assert = new Assert();
        $assert->isapi_keyValid($config);
        $this->api_key = $config['api_key'];

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

        isset($config['event_listener']) ?
            $this->event_listener = $config['event_listener'] :
            $this->event_listener =  new ShipEngineEventListener();

        $this->base_url = $config['base_url'] ?? self::DEFAULT_BASE_URI;
        $this->page_size = $config['page_size'] ?? self::DEFAULT_PAGE_SIZE;
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

        isset($newConfig['api_key']) ?
            ($config['api_key'] = $newConfig['api_key']) :
            ($config['api_key'] = $this->api_key);

        isset($newConfig['base_url']) ?
            ($config['base_url'] = $newConfig['base_url']) :
            ($config['base_url'] = $this->base_url);

        isset($newConfig['page_size']) ?
            ($config['page_size'] = $newConfig['page_size']) :
            ($config['page_size'] = $this->page_size);

        isset($newConfig['retries']) ?
            ($config['retries'] = $newConfig['retries']) :
            ($config['retries'] = $this->retries);

        isset($newConfig['timeout']) ?
            ($config['timeout'] = $newConfig['timeout']) :
            ($config['timeout'] = $this->timeout);

        isset($newConfig['$this->event_listener']) ?
            ($config['$this->event_listener'] = $newConfig['$this->event_listener']) :
            ($config['$this->event_listener'] = $this->event_listener);

        return new ShipEngineConfig($config);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     */
    public function jsonSerialize()
    {
        return [
          'api_key' => $this->api_key,
          'base_url' => $this->base_url,
          'page_size' => $this->page_size,
          'retries' => $this->retries,
          'timeout' => $this->timeout->s,
          'event_listener' => $this->event_listener
        ];
    }
}
