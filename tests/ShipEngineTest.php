<?php declare(strict_types=1);

namespace ShipEngine\Test;

use PHPUnit\Framework\TestCase;

use ShipEngine\ShipEngine;

/**
 * @covers \ShipEngine\ShipEngine
 * @covers \ShipEngine\ShipEngineClient
 * @covers \ShipEngine\Service\ServiceFactory
 */
final class ShipEngineTest extends TestCase
{

    private $default_config = array('api_key' => 'FOOBAR');
    
    public function testApiKeyMissing(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $config = array();
        new ShipEngine($config);
    }

    public function testBaseUriMalformed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $config = $this->default_config;
        $config['base_uri'] = 'foo/bar/baz';
        new ShipEngine($config);
    }
    
    public function testPageSizeAboveBounds(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $config = $this->default_config;
        $config['page_size'] = ShipEngine::MAXIMUM_PAGE_SIZE + 1;
        new ShipEngine($config);
    }
    
    public function testPageSizeBelowBounds(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $config = $this->default_config;
        $config['page_size'] = ShipEngine::MINIMUM_PAGE_SIZE - 1;
        new ShipEngine($config);
    }

    public function testRetriesAboveBounds(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $config = $this->default_config;
        $config['retries'] = ShipEngine::MAXIMUM_RETRIES + 1;
        new ShipEngine($config);
    }
    
    public function testRetriesBelowBounds(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $config = $this->default_config;
        $config['retries'] = ShipEngine::MINIMUM_RETRIES - 1;
        new ShipEngine($config);
    }

    public function testShipEngineConstructor(): void
    {
        $config = $this->default_config;
        $config['base_uri'] = ShipEngine::DEFAULT_BASE_URI;
        $config['page_size'] = ShipEngine::DEFAULT_PAGE_SIZE;
        $config['retries'] = ShipEngine::DEFAULT_RETRIES;

        $shipengine = new ShipEngine($config);
        
        $this->assertInstanceOf(ShipEngine::class, $shipengine);
    }
}
