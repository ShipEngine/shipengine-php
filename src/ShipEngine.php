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
use ShipEngine\Service\AddressesTrait;
use ShipEngine\Service\TagsTrait;

/**
 * ShipEngine client.
 *
 * @property \ShipEngine\Service\AddressesService $addresses
 */
final class ShipEngine
{
    use AddressesTrait;

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
        if (!array_key_exists('base_uri', $config)) {
            $config['base_uri'] = self::DEFAULT_BASE_URI;
        }

        if (!array_key_exists('page_size', $config)) {
            $config['page_size'] = self::DEFAULT_PAGE_SIZE;
        }

        if (!array_key_exists('retries', $config)) {
            $config['retries'] = self::DEFAULT_RETRIES;
        }

        $this->validateConfig($config);

        $client = $this->initializeShipEngineClient($config, $client);
        
        $this->service_factory = new ServiceFactory($client);
    }
    
    public function __get($name)
    {
        return $this->service_factory->__get($name);
    }
    
    /**
     * Valideate api_key.
     */
    private function validateApiKey(string $api_key): string
    {
        return '';
    }
    
    /**
     * Validate base_uri.
     */
    private function validateBaseUri(string $base_uri): string
    {
        if (!filter_var($base_uri, FILTER_VALIDATE_URL)) {
            return 'The given base URI is malformed.';
        }

        return '';
    }

    /**
     * Validate page_size.
     */
    private function validatePageSize(int $page_size): string
    {
        $messages = array();

        if ($page_size < self::MINIMUM_PAGE_SIZE) {
            $messages[] = 'Page size must be greater than ' . self::MINIMUM_PAGE_SIZE . '.';
        }
        if ($page_size > self::MAXIMUM_PAGE_SIZE) {
            $messages[] = 'Page size must be less than ' . self::MAXIMUM_PAGE_SIZE . '.';
        }

        if (count($messages) > 0) {
            return implode(' ', $messages);
        }

        return '';
    }

    /**
     * Validate retries.
     */
    private function validateRetries(int $retries): string
    {
        $messages = array();

        if ($retries < self::MINIMUM_RETRIES) {
            $messages[] = 'Retries must be greater than ' . self::MINIMUM_RETRIES . '.';
        }
        if ($retries > self::MAXIMUM_RETRIES) {
            $messages[] = 'Retries must be less than ' . self::MAXIMUM_RETRIES . '.';
        }
        
        if (count($messages) > 0) {
            return implode(' ', $messages);
        }

        return '';
    }

    /**
     * Validate the entirety of the config object.
     */
    private function validateConfig(array $config)
    {
        $messages = array();
        
        if (!array_key_exists('api_key', $config)) {
            $messages[] = 'An API Key is required.';
        } else {
            $api_key_messages = $this->validateApiKey($config['api_key']);
            if ($api_key_messages !== '') {
                $messages[] = $api_key_messages;
            }
        }
        
        if (array_key_exists('base_uri', $config)) {
            $base_uri_messages = $this->validateBaseUri($config['base_uri']);
            if ($base_uri_messages !== '') {
                $messages[] = $base_uri_messages;
            }
        }

        if (array_key_exists('page_size', $config)) {
            $page_size_messages = $this->validatePageSize($config['page_size']);
            if ($page_size_messages !== '') {
                $messages[] = $page_size_messages;
            }
        }

        if (array_key_exists('retries', $config)) {
            $retries_messages = $this->validateRetries($config['retries']);
            if ($retries_messages !== '') {
                $messages[] = $retries_messages;
            }
        }
        
        if (count($messages) > 0) {
            throw new \InvalidArgumentException(implode(' ', $messages));
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
     * Initialize a HttpClient.
     */
    private function initializeShipEngineClient(array $config, HttpClient $client = null): ShipEngineClient
    {
        if (!$client) {
            $client = HttpClientDiscovery::find();
        }
        
        $headers = array();
        $headers['Api-Key'] = $config['api_key'];
        $headers['Content-Type'] = 'application/json';
        $headers['User-Agent'] = $this->deriveUserAgent();

        $uri_factory = UriFactoryDiscovery::find();
        $base_uri = $uri_factory->createUri($config['base_uri']);
        
        $plugins = array();
        $plugins[] = new HeaderDefaultsPlugin($headers);
        $plugins[] = new BaseUriPlugin($base_uri);
        $plugins[] = new RetryPlugin(['retries' => $config['retries']]);

        return new ShipEngineClient($client, $plugins, $config);
    }
}
