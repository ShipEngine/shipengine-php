<?php declare(strict_types=1);

namespace ShipEngine\Test;

use PHPUnit\Framework\TestCase;

use ShipEngine\ShipEngine;
use ShipEngine\ShipEngineConfig;

/**
 * @covers \ShipEngine\ShipEngine
 * @covers \ShipEngine\ShipEngineClient
 * @covers \ShipEngine\ShipEngineConfig
 * @covers \ShipEngine\Service\ServiceFactory
 */
final class ShipEngineTest extends TestCase
{
    public function testShipEngineConstructor(): void
    {
        $config = array('api_key' => 'PHP');
        $config['base_uri'] = ShipEngineConfig::DEFAULT_BASE_URI;
        $config['page_size'] = ShipEngineConfig::DEFAULT_PAGE_SIZE;
        $config['retries'] = ShipEngineConfig::DEFAULT_RETRIES;

        $shipengine = new ShipEngine($config);
        
        $this->assertInstanceOf(ShipEngine::class, $shipengine);
    }
}
