<?php declare(strict_types=1);

namespace ShipEngine;

use Http\Client\Common\PluginClient;
use Http\Client\Common\Plugin\BaseUriPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\Plugin\RetryPlugin;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\UriFactoryDiscovery;

use ShipEngine\Service\ServiceFactory;

/**
 * ShipEngine client.
 */
final class ShipEngine
{
    const VERSION = '0.0.1';

    const DEFAULT_BASE_URI = 'https://api.shipengine.com/v1/';
    
    const DEFAULT_PAGE_SIZE = 10;
    const MAXIMUM_PAGE_SIZE = 100;
    const MINIMUM_PAGE_SIZE = 1;

    const DEFAULT_RETRIES = 0;
    const MAXIMUM_RETRIES = 3;
    const MINIMUM_RETRIES = 0;

    /**
     * Factory providing services.
     */
    private ServiceFactory $service_factory;

    public function __construct(array $config = array(), HttpClient $client = null)
    {
        if (!array_key_exists('api_key', $config)) {
            throw new \InvalidArgumentException('An API Key is required.');
        }
        $this->validateApiKey($config['api_key']);
        $api_key = $config['api_key'];
        
        if (array_key_exists('base_uri', $config)) {
            $this->validateBaseUri($config['base_uri']);
            $base_uri = $config['base_uri'];
        } else {
            $base_uri = self::DEFAULT_BASE_URI;
        }

        if (array_key_exists('page_size', $config)) {
            $this->validatePageSize($config['page_size']);
            $page_size = $config['page_size'];
        } else {
            $page_size = self::DEFAULT_PAGE_SIZE;
        }

        if (array_key_exists('retries', $config)) {
            $this->validateRetries($config['retries']);
            $retries = $config['retries'];
        } else {
            $retries = self::DEFAULT_RETRIES;
        }

        $client = $this->createClient($client, $api_key, $base_uri, $retries);

        $this->service_factory = new ServiceFactory($client, $page_size);
    }
        
    public function __get($name)
    {
        return $this->serviceFactory->__get($name);
    }

    /**
     * Valideate api_key.
     */
    private function validateApiKey(string $api_key)
    {
    }
    
    /**
     * Validate base_uri.
     */
    private function validateBaseUri(string $base_uri)
    {
        if (!filter_var($base_uri, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('The given URI is malformed.');
        }
    }

    /**
     * Validate page_size.
     */
    private function validatePageSize(int $page_size)
    {
        $exceptions = array();
        if ($page_size < self::MINIMUM_PAGE_SIZE) {
            $exceptions[] = 'Page size must be greater than ' . self::MINIMUM_PAGE_SIZE . '.';
        }
        if ($page_size > self::MAXIMUM_PAGE_SIZE) {
            $exceptions[] = 'Page size must be less than ' . self::MAXIMUM_PAGE_SIZE . '.';
        }
        if (count($exceptions) > 0) {
            throw new \InvalidArgumentException(implode(' ', $exceptions));
        }
    }

    /**
     * Validate retries.
     */
    private function validateRetries(int $retries)
    {
        $exceptions = array();
        if ($retries < self::MINIMUM_RETRIES) {
            $exceptions[] = 'Retries must be greater than ' . self::MINIMUM_RETRIES . '.';
        }
        if ($retries > self::MAXIMUM_RETRIES) {
            $exceptions[] = 'Retries must be less than ' . self::MAXIMUM_RETRIES . '.';
        }
        if (count($exceptions) > 0) {
            throw new \InvalidArgumentException(implode(' ', $exceptions));
        }
    }

    /**
     * Derive a User-Agent header from the environment.
     */
    private function deriveUserAgent(): string
    {
        $sdk_version = 'shipengine-php/' . self::VERSION;
        
        $os = explode(' ', php_uname());
        $os_kernel = $os[0] . '/' . $os[2];

        $php_version = 'PHP/' . phpversion();

        return $sdk_version . ' ' . $os_kernel . ' ' . $php_version;
    }
    
    /**
     * Create a HttpClient.
     */
    private function createClient(HttpClient $client = null, $api_key, $base_uri, $retries): HttpClient
    {
        if (!$client) {
            $client = HttpClientDiscovery::find();
        }
        
        $headers = array();
        $headers['Api-Key'] = $api_key;
        $headers['Content-Type'] = 'application/json';
        $headers['User-Agent'] = $this->deriveUserAgent();

        $uri_factory = UriFactoryDiscovery::find();
        $base_uri = $uri_factory->createUri($base_uri);
        
        $plugins = array();
        $plugins[] = new HeaderDefaultsPlugin($headers);
        $plugins[] = new BaseUriPlugin($base_uri);
        $plugins[] = new RetryPlugin(['retries' => $retries]);

        return new PluginClient($client, $plugins);
    }
}
