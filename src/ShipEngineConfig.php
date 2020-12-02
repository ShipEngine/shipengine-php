<?php declare(strict_types=1);

namespace ShipEngine;

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
        $messages = array();

        if (!array_key_exists('api_key', $config)) {
            $messages[] = 'An API Key is required.';
        } else {
            $this->api_key = $config['api_key'];
        }

        if (array_key_exists('base_uri', $config)) {
            $base_uri_messages = $this->validateBaseUri($config['base_uri']);
            if (!is_null($base_uri_messages)) {
                $messages[] = $base_uri_messages;
            } else {
                $this->base_uri = $config['base_uri'];
            }
        } else {
            $this->base_uri = self::DEFAULT_BASE_URI;
        }

        if (array_key_exists('page_size', $config)) {
            $page_size_messages = $this->validatePageSize($config['page_size']);
            if (!is_null($page_size_messages)) {
                $messages[] = $page_size_messages;
            } else {
                $this->page_size = $config['page_size'];
            }
        } else {
            $this->page_size = self::DEFAULT_PAGE_SIZE;
        }

        if (array_key_exists('retries', $config)) {
            $retries_messages = $this->validateRetries($config['retries']);
            if (!is_null($retries_messages)) {
                $messages[] = $retries_messages;
            } else {
                $this->retries = $config['retries'];
            }
        } else {
            $this->retries = self::DEFAULT_RETRIES;
        }

        if (!array_key_exists('user_agent', $config)) {
            $messages[] = 'A user agent is required.';
        } else {
            $this->user_agent = $config['user_agent'];
        }
        
        if (!empty($messages)) {
            throw new \InvalidArgumentException(implode(' ', $messages));
        }
    }
        
    /**
     * Validate base_uri.
     */
    private function validateBaseUri(string $base_uri): ?string
    {
        if (!filter_var($base_uri, FILTER_VALIDATE_URL)) {
            return 'The given base URI is malformed.';
        }

        return null;
    }

    /**
     * Validate page_size.
     */
    private function validatePageSize(int $page_size): ?string
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

        return null;
    }

    /**
     * Validate retries.
     */
    private function validateRetries(int $retries): ?string
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

        return null;
    }
}
