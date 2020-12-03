<?php declare(strict_types=1);

namespace ShipEngine;

use Rakit\Validation\Validator;

use ShipEngine\Util;

/**
 * Configuration.
 *
 * @property string $api_key
 * @property string $base_uri
 * @property int $page_size
 * @property int $retries
 * @property string $user_agent
 */
final class ShipEngineConfig
{
    use Util\Getters;
    
    const DEFAULT_BASE_URI = 'https://api.shipengine.com/v1/';
    
    const DEFAULT_PAGE_SIZE = 10;
    const MAXIMUM_PAGE_SIZE = 100;
    const MINIMUM_PAGE_SIZE = 1;

    const DEFAULT_RETRIES = 0;
    const MAXIMUM_RETRIES = 3;
    const MINIMUM_RETRIES = 0;

    private string $api_key;
    private string $base_uri;
    private int $page_size;
    private int $retries;
    private string $user_agent;
    
    public function __construct(array $config = array())
    {
        $validator = new Validator();

        $base_uri_guard = sprintf('default:%s|url:http,https', self::DEFAULT_BASE_URI);

        $range = 'default:%s|between:%s,%s';
        $page_size_guard = sprintf($range, self::DEFAULT_PAGE_SIZE, self::MINIMUM_PAGE_SIZE, self::MAXIMUM_PAGE_SIZE);
        $retries_guard = sprintf($range, self::DEFAULT_RETRIES, self::MINIMUM_RETRIES, self::MAXIMUM_RETRIES);

        $guard = array(
            'api_key' => 'required',
            'base_uri' => $base_uri_guard,
            'page_size' => $page_size_guard,
            'retries' => $retries_guard,
            'user_agent' => 'required'
        );

        $validation = $validator->validate($config, $guard);
        
        if ($validation->fails()) {
            throw new \InvalidArgumentException(implode(' ', $validation->errors()->all()));
        }

        $validated = $validation->getValidData();

        $this->api_key = $validated['api_key'];
        $this->base_uri = $validated['base_uri'];
        $this->page_size = (int) $validated['page_size'];
        $this->retries = (int) $validated['retries'];
        $this->user_agent = $validated['user_agent'];
    }
}
