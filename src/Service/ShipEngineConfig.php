<?php declare(strict_types=1);

namespace ShipEngine\Service;

use Http\Client\HttpClient;
use ShipEngine\Message\ShipEngineValidationException;
use ShipEngine\Util;

final class ShipEngineConfig
{
    use Util\Getters;

    const DEFAULT_BASE_URI = 'https://api.shipengine.com';
    const DEFAULT_PAGE_SIZE = 50;
    const DEFAULT_RETRIES = 1;
    const DEFAULT_TIMEOUT = 5000;

    public string $api_key;
    public string $base_url;
    public int $page_size;
    public int $retries;
    public int $timeout;
    public ?HttpClient $client = null;

    /**
     * ShipEngineConfig constructor.
     *
     * @param array $config {api_key:string, base_url:string, page_size:int,
     * retries:int, timeout:int, client:HttpClient|null}
     */
    public function __construct(array $config = array())
    {
        if (isset($config['api_key']) === false || $config['api_key'] === '') {
            throw new ShipEngineValidationException(
                'A ShipEngine API key must be specified.',
                null,
                'shipengine',
                'validation',
                'field_value_required'
            );
        } else {
            $this->api_key = $config['api_key'];
        }

        if (isset($config['retries']) === true && $config['retries'] > 0) {
            $this->retries = $config['retries'];
        } elseif (isset($config['retries']) === false) {
            $this->retries = self::DEFAULT_RETRIES;
        } elseif ($config['retries'] <= 0) {
            throw new ShipEngineValidationException(
                'Retries must be zero or greater.',
                null,
                'shipengine',
                'validation',
                'invalid_field_value'
            );
        }

        if (isset($config['timeout']) === true && $config['timeout'] <= 0) {
            throw new ShipEngineValidationException(
                'Timeout must be greater than zero.',
                null,
                'shipengine',
                'validation',
                'invalid_field_value'
            );
        } elseif (isset($config['timeout']) === false) {
            $this->retries = self::DEFAULT_RETRIES;
        } else {
            $this->timeout = $config['timeout'];
        }

        if (isset($config['client']) === true) {
            $this->client = $config['client'];
        }

        $this->base_url = isset($config['base_url']) ? $config['base_url'] : self::DEFAULT_BASE_URI;
        $this->page_size = isset($config['page_size']) ? $config['page_size'] : self::DEFAULT_PAGE_SIZE;
    }

    public function merge(array $new_config): ShipEngineConfig
    {
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

        isset($new_config['client']) ?
            ($config['client'] = $new_config['client']) :
            ($config['client'] = $this->client);

//        if (isset($new_config['api_key'])) {
//            $config['api_key'] = $new_config['api_key'];
//        } else {
//            $api_key = $this->api_key;
//        }

        return new ShipEngineConfig($config);
    }

    public function checkConfig(): ShipEngineConfig
    {
        return $this;
    }
}
