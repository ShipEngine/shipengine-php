<?php declare(strict_types=1);

namespace ShipEngine\Service;

use DateInterval;
use ShipEngine\Message\Events\ShipEngineEventListener;
use ShipEngine\Message\ValidationException;
use ShipEngine\Util;
use ShipEngine\Util\Assert;

final class ShipEngineConfig
{
    use Util\Getters;

    const DEFAULT_BASE_URI = 'https://api.shipengine.com/jsonrpc';
    const DEFAULT_PAGE_SIZE = 50;
    const DEFAULT_RETRIES = 1;
    const DEFAULT_TIMEOUT = 'PT5S';

    public string $api_key;
    public string $base_url;
    public int $page_size;
    public int $retries;
    public DateInterval $timeout;
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
        $assert->isApiKeyValid($config);
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

        // TODO: EVENT LISTENER - debug to ensure this is working properly to set the default listener.
        isset($config['event_listener']) ?
            $this->event_listener = $config['event_listener'] :
            $this->event_listener =  new ShipEngineEventListener();

        $this->base_url = $config['base_url'] ?? self::DEFAULT_BASE_URI;
        $this->page_size = $config['page_size'] ?? self::DEFAULT_PAGE_SIZE;
    }

    public function merge(?array $new_config): ShipEngineConfig
    {
        if (!isset($new_config)) {
            return $this;
        }

        $config = array();

        isset($new_config['api_key']) ?
            ($config['api_key'] = $new_config['api_key']) :
            ($config['api_key'] = $this->api_key);

        isset($new_config['base_url']) ?
            ($config['base_url'] = $new_config['base_url']) :
            ($config['base_url'] = $this->base_url);

        isset($new_config['page_size']) ?
            ($config['page_size'] = $new_config['page_size']) :
            ($config['page_size'] = $this->page_size);

        isset($new_config['retries']) ?
            ($config['retries'] = $new_config['retries']) :
            ($config['retries'] = $this->retries);

        isset($new_config['timeout']) ?
            ($config['timeout'] = $new_config['timeout']) :
            ($config['timeout'] = $this->timeout);

        isset($new_config['timeout']) ?
            ($config['timeout'] = $new_config['timeout']) :
            ($config['timeout'] = $this->timeout);

        isset($new_config['$this->event_listener']) ?
            ($config['$this->event_listener'] = $new_config['$this->event_listener']) :
            ($config['$this->event_listener'] = $this->event_listener);

        return new ShipEngineConfig($config);
    }
}
