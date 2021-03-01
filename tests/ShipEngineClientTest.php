<?php declare(strict_types=1);

namespace ShipEngine\Test;

use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;

use PHPUnit\Framework\TestCase;

use ShipEngine\ShipEngineClient;
use ShipEngine\ShipEngineConfig;

/**
 * @covers \ShipEngine\ShipEngineClient
 */
final class ShipEngineClientTest extends TestCase
{
    private MessageFactory $message_factory;
    
    public static function setupBeforeClass(): void
    {
        putenv("RPC_CLIENT_BASE_URI=http://localhost:8500");
        exec('hoverctl import simengine/client/retry/429.json');
    }

    public static function teardownAfterClass(): void
    {
        putenv("RPC_CLIENT_BASE_URI");
        exec('hoverctl state --force delete-all');
        exec('hoverctl delete --force simengine/client/retry/429.json');
    }

    public function setUp(): void
    {
        $this->message_factory = MessageFactoryDiscovery::find();
    }
    
    public function testRetries(): void
    {
        $user_agent = 'PHP';
        $api_key = 'baz';
        $client = new ShipEngineClient($api_key, $user_agent);

        $request = $this->message_factory->createRequest('GET', '/retries');
        $response = $client->sendRequest($request);
        
        $this->assertEquals(200, $response->getStatusCode());
    }
}
