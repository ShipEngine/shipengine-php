<?php declare(strict_types=1);

namespace ShipEngine;

use ShipEngine\Exceptions\ConfigException;

/**
 *
 */
final class Config
{
    private $api_key;
    private $paging;
    private $proxy;
    private $retry;

    /**
     *
     */
    private function __construct()
    {
    }

    /**
     *
     */
    private function validateApiKey(string $api_key): bool
    {
        return true;
    }
    
    /**
     *
     */
    public function setApiKey(string $api_key)
    {
        if ($this->validateApiKey($api_key)) {
            $this->api_key = $api_key;
        }
        throw new ConfigException('API Key is not valid.');
    }

    /**
     *
     */
    public function getApiKey(): string
    {
        return $this->api_key;
    }

    /**
     *
     */
    private function validatePaging()
    {
    }
    
    /**
     *
     */
    public function setPaging()
    {
    }

    /**
     *
     */
    public function getPaging()
    {
    }

    /**
     *
     */
    private function validateProxy()
    {
    }

    /**
     *
     */
    public function setProxy()
    {
    }

    /**
     *
     */
    public function getProxy()
    {
    }

    /**
     *
     */
    private function validateRetry()
    {
    }

    /**
     *
     */
    public function setRetry()
    {
    }

    /**
     *
     */
    public function getRetry()
    {
    }
}
