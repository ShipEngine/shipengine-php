<?php declare(strict_types=1);

namespace ShipEngine\Test;

use PHPUnit\Framework\TestCase;

use ShipEngine\ShipEngineConfig;

/**
 * @covers \ShipEngine\ShipEngineConfig
 */
final class ShipEngineConfigTest extends TestCase
{
    private array $config = array('api_key' => 'PHP');
    
    public function testApiKeyMissing(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ShipEngineConfig(array());
    }

    public function testBaseUriMalformed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->config['base_uri'] = 'foo/bar/baz';
        new ShipEngineConfig($this->config);
    }
    
    public function testPageSizeAboveBounds(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->config['page_size'] = ShipEngineConfig::MAXIMUM_PAGE_SIZE + 1;
        new ShipEngineConfig($this->config);
    }
    
    public function testPageSizeBelowBounds(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->config['page_size'] = ShipEngineConfig::MINIMUM_PAGE_SIZE - 1;
        new ShipEngineConfig($this->config);
    }

    public function testRetriesAboveBounds(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->config['retries'] = ShipEngineConfig::MAXIMUM_RETRIES + 1;
        new ShipEngineConfig($this->config);
    }
    
    public function testRetriesBelowBounds(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->config['retries'] = ShipEngineConfig::MINIMUM_RETRIES - 1;
        new ShipEngineConfig($this->config);
    }
}
