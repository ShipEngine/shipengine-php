<?php declare(strict_types=1);

namespace ShipEngine\Test;

use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;

use PHPUnit\Framework\TestCase;

use ShipEngine\ShipEngineClient;
use ShipEngine\ShipEngineConfig;

/**
 * @covers \ShipEngine\ShipEngineClient
 * @covers \ShipEngine\ShipEngineConfig
 */
final class ShipEngineClientTest extends TestCase
{
    private array $config;
    private MessageFactory $message_factory;
    
    public static function setupBeforeClass(): void
    {
        exec('hoverctl import simengine/client/retry/429.json');
    }

    public static function teardownAfterClass(): void
    {
        exec('hoverctl state --force delete-all');
        exec('hoverctl delete --force simengine/client/retry/429.json');
    }

    public function setUp(): void
    {
        $this->config = array(
            'api_key' => 'TEST',
            'base_uri' => 'http://localhost:8500',
            'user_agent' => 'TEST'
        );
        $this->message_factory = MessageFactoryDiscovery::find();
    }
    
    public function testRetries(): void
    {
        $this->config['retries'] = 1;
        
        $config = new ShipEngineConfig($this->config);
        $client = new ShipEngineClient($config);

        $request = $this->message_factory->createRequest('GET', '/retries');
        $response = $client->sendRequest($request);
        
        $this->assertEquals(200, $response->getStatusCode());
    }
}
