<?php declare(strict_types=1);

namespace ShipEngine\Service;

use ShipEngine\Message\ShipEngineValidationError;
use ShipEngine\Util;

final class ShipEngineConfig
{
    use Util\Getters;

    const DEFAULT_BASE_URI = 'https://api.shipengine.com';
    const DEFAULT_PAGE_SIZE = 50;
    const DEFAULT_RETRIES = 1;
    const DEFAULT_TIMEOUT = 5000;
    const DEFAULT_EVENTS = null;

    public string $api_key;
    public string $base_url;
    public int $page_size;
    public int $retries;
    public int $timeout;  //TODO: get confirmation on how to enforce this in the client.
    public ?string $events;

    /**
     * ShipEngineConfig constructor.
     *
     * @param array $config {api_key:string, base_url:string, page_size:ing,
     * retries:int, timeout:int, log:string}
     */
    public function __construct(array $config = array())
    {
        if (isset($config['api_key']) === false || $config['api_key'] === '') {
            throw new ShipEngineValidationError(
                'A ShipEngine API key must be specified.',
                null,
                'shipengine',
                'validation',
                'field_value_required'
            );
        } else {
            $this->api_key = $config['api_key'];
        }

        if ($config['retries'] < 0) {
            throw new ShipEngineValidationError(
                'Retries must be zero or greater.',
                null,
                'shipengine',
                'validation',
                'invalid_field_value'
            );
        } elseif (isset($config['retries']) === false) {
            $this->retries = self::DEFAULT_RETRIES;
        } else {
            $this->retries = $config['retries'];
        }

        if (isset($config['timeout']) === true && $config['timeout'] <= 0) {
            throw new ShipEngineValidationError(
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

//        if (isset($config['']) === false) {
//
//        }
//
//        if (isset($config['']) === false) {
//
//        }
//
//        if (isset($config['']) === false) {
//
//        }

        $this->base_url = isset($config['base_url']) ? $config['base_url'] : self::DEFAULT_BASE_URI;
        $this->page_size = isset($config['page_size']) ? $config['page_size'] : self::DEFAULT_PAGE_SIZE;
        $this->timeout = isset($config['timeout']) ? $config['timeout'] : self::DEFAULT_TIMEOUT;
        $this->events = isset($config['events']) ? $config['events'] : self::DEFAULT_EVENTS;
    }

    public function updateApiKey(string $api_key): ShipEngineConfig
    {
        $this->api_key = $api_key;
        return $this;
    }

    public function updateRetries(int $retries): ShipEngineConfig
    {
        $this->retries = $retries;
        return $this;
    }

    public function updateTimeout(int $timeout): ShipEngineConfig
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function updateBaseUrl(string $base_url): ShipEngineConfig
    {
        $this->base_url = $base_url;
        return $this;
    }

    public function updatePageSize(int $page_size): ShipEngineConfig
    {
        $this->page_size = $page_size;
        return $this;
    }

    public function checkConfig(): ShipEngineConfig
    {
        return $this;
    }
}
