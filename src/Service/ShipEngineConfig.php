<?php declare(strict_types=1);

namespace ShipEngine\Service;

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
        $this->api_key = $config['api_key'];
        $this->base_url = isset($config['base_url']) ? $config['base_url'] : self::DEFAULT_BASE_URI;
        $this->page_size = isset($config['page_size']) ? $config['page_size'] : self::DEFAULT_PAGE_SIZE;
        $this->retries = isset($config['retries']) ? $config['retries'] : self::DEFAULT_RETRIES;
        $this->timeout = isset($config['timeout']) ? $config['timeout'] : self::DEFAULT_TIMEOUT;
        $this->events = isset($config['events']) ? $config['events'] : self::DEFAULT_EVENTS;
    }
}
