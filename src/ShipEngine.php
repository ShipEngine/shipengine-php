<?php declare(strict_types=1);

namespace ShipEngine;

use Http\Client\HttpClient;

use ShipEngine\Service\TagsTrait;
use ShipEngine\Service\ServiceFactory;

/**
 * ShipEngine client.
 *
 * @property \ShipEngine\Service\TagsService $tags
 */
final class ShipEngine
{
    // Convenience method Traits.
    use TagsTrait;
    
    // Factory providing services.
    private ServiceFactory $service_factory;

    const VERSION = '0.0.1';
    
    public function __construct(string $api_key, HttpClient $client = null)
    {
        $client = new ShipEngineClient($api_key, $this->deriveUserAgent(), $client);
        
        $this->service_factory = new ServiceFactory($client);
    }
    
    public function __get($name)
    {
        return $this->service_factory->__get($name);
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
}
