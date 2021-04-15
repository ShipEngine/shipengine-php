<?php declare(strict_types=1);

namespace ShipEngine\Service;

use DateInterval;
use Http\Client\HttpClient;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\Message\ValidationException;
use ShipEngine\Util;

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

    /**
     * ShipEngineConfig constructor.
     *
     * @param array $config {api_key:string, base_url:string, page_size:int,
     * retries:int, timeout:int, client:HttpClient|null}
     */
    public function __construct(array $config = array())
    {
        // TODO: move the following validations into the Assert Class.
        if (isset($config['api_key']) === false || $config['api_key'] === '') {
            throw new ValidationException(
                'A ShipEngine API key must be specified.',
                null,
                'shipengine',
                'validation',
                'field_value_required'
            );
        } else {
            $this->api_key = $config['api_key'];
        }

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
            if ($timeout->invert === 1 || $timeout->s === 0) {
                throw new ValidationException(
                    'Timeout must be greater than zero.',
                    null,
                    'shipengine',
                    'validation',
                    'invalid_field_value'
                );
            }
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

        return new ShipEngineConfig($config);
    }

    public function checkConfig(): ShipEngineConfig
    {
        return $this;
    }
}
