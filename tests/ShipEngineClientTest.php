<?php declare(strict_types=1);

namespace ShipEngine\Test;

use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\MessageFactory;

use PHPUnit\Framework\TestCase;

use ShipEngine\ShipEngineClient;

/**
 * @covers \ShipEngine\ShipEngineClient
 */
final class ShipEngineClientTest extends TestCase
{
    private MessageFactory $message_factory;
    
    public function setUp(): void
    {
        $this->config = array(
            'api_key' => 'TEST',
            'user_agent' => 'TEST'
        );
        $this->message_factory = MessageFactoryDiscovery::find();
    }

    public function testRetries(): void
    {
        $client = new ShipEngineClient($this->config['api_key'], $this->config['user_agent']);

        $request = $this->message_factory->createRequest('GET', '/retries');
        $response = $client->sendRequest($request);
        
        $this->assertEquals(200, $response->getStatusCode());
    }
}
