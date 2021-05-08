<?php declare(strict_types=1);

namespace ShipEngine\Message\Events;

use DateInterval;
use DateTime;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use ShipEngine\Message\Events\RequestSentEvent;
use ShipEngine\Model\Address\Address;
use ShipEngine\ShipEngine;
use ShipEngine\Util\Constants\Endpoints;

/**
 * @covers \ShipEngine\Message\Events\RequestSentEvent
 */
final class RequestSentEventTest extends MockeryTestCase
{
    public function testRequestSent(): void
    {  
        $spy = \Mockery::spy('ShipEngineEventListener');      
        $config_options = $this->stubConfig($spy);
        $ship_engine = new ShipEngine($config_options);
        $good_address = $this->stubAddress();
    
        $ship_engine->validateAddress($good_address);
        
        $event_result = null;
        $spy->shouldHaveReceived('onRequestSent')
            ->withArgs(function ($event) use (&$event_result) {
                $event_result = $event;
                return true;
            })
            ->once();

        $this->assertRequestEvent($event_result, $config_options);     
    }

    public function assertRequestEvent($event, $config_options) : void {
        $this->assertInstanceOf(RequestSentEvent::class, $event);
        $this->assertEqualsWithDelta($event->timestamp, new DateTime(), 5);
        $this->assertEquals($event->type, RequestSentEvent::REQUEST_SENT);
        $this->assertEquals($event->message, $this->expectedMessage($config_options));
        $this->assertEquals($event->url, $config_options['base_url']);
        $this->assertEquals($event->headers['Api-Key'], $config_options['api_key']);
        $this->assertEquals($event->headers['Content-Type'], 'application/json');
        $this->assertEquals($event->body['method'], 'address/validate');
        $this->assertEquals($event->retry, 0);
        $this->assertEquals($event->timeout, $config_options['timeout']);
    }

    private function expectedMessage($config_options) : string {
        $url = $config_options['base_url'];
        return "Calling the ShipEngine address/validate API at {$url}";
    }

    private function stubAddress() : Address {
        return new Address(
            array(
                'street' => array('11222 Washington Pl'),
                'city_locality' => 'Culver City',
                'state_province' => 'CA',
                'postal_code' => '90230',
                'country_code' => 'US',
            )
        );
    }

    private function stubConfig($event_listener) : array {
        return array(
            'api_key' => 'baz',
            'base_url' => Endpoints::TEST_RPC_URL,
            'timeout' => new DateInterval('PT15000S'),
            'event_listener' => $event_listener
        );
    }
}
