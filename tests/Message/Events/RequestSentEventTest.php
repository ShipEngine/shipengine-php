<?php declare(strict_types=1);

namespace ShipEngine\Message\Events;

use DateInterval;
use DateTime;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use ShipEngine\Model\Address\Address;
use ShipEngine\ShipEngine;
use ShipEngine\Util\Constants\Endpoints;
use ShipEngine\Util\Constants\RPCMethods;

/**
 * @covers \ShipEngine\Message\Events\RequestSentEvent
 * @covers \ShipEngine\Message\Events\ResponseReceivedEvent
 * @covers \ShipEngine\Message\Events\ShipEngineEvent
 * @covers \ShipEngine\Message\Events\ShipEngineEventListener
 * @uses \ShipEngine\Model\Address\Address
 * @uses \ShipEngine\Model\Address\AddressValidateResult
 * @uses \ShipEngine\Service\Address\AddressService
 * @uses \ShipEngine\ShipEngine
 * @uses \ShipEngine\ShipEngineClient
 * @uses \ShipEngine\ShipEngineConfig
 * @uses \ShipEngine\Util\Assert
 * @uses \ShipEngine\Util\VersionInfo
 */
final class RequestSentEventTest extends MockeryTestCase
{
    public function testRequestSent(): void
    {
        $spy = \Mockery::spy('ShipEngineEventListener');
        $configOptions = $this->stubConfig($spy);
        $ship_engine = new ShipEngine($configOptions);
        $good_address = $this->stubAddress();

        $ship_engine->validateAddress($good_address);

        $eventResult = null;
        $spy->shouldHaveReceived('onRequestSent')
            ->withArgs(function ($event) use (&$eventResult) {
                $eventResult = $event;
                return true;
            })
            ->once();

        $this->assertRequestEvent($eventResult, $configOptions);
    }

    public function assertRequestEvent($event, $configOptions) : void
    {
        $this->assertInstanceOf(RequestSentEvent::class, $event);
        $this->assertEqualsWithDelta($event->timestamp, new DateTime(), 5);
        $this->assertEquals($event->type, RequestSentEvent::REQUEST_SENT);
        $this->assertEquals($event->message, $this->expectedMessage($configOptions));
        $this->assertEquals($event->url, $configOptions['baseUrl']);
        $this->assertEquals($event->headers['Api-Key'], $configOptions['apiKey']);
        $this->assertEquals($event->headers['Content-Type'], 'application/json');
        $this->assertEquals($event->body['method'], RPCMethods::ADDRESS_VALIDATE);
        $this->assertEquals($event->retry, 0);
        $this->assertEquals($event->timeout, $configOptions['timeout']);
    }

    private function expectedMessage($configOptions) : string
    {
        $url = $configOptions['baseUrl'];
        $method = RPCMethods::ADDRESS_VALIDATE;
        return "Calling the ShipEngine $method API at $url";
    }

    private function stubAddress() : Address
    {
        return new Address(
            array(
                'street' => array('11222 Washington Pl'),
                'cityLocality' => 'Culver City',
                'stateProvince' => 'CA',
                'postalCode' => '90230',
                'countryCode' => 'US',
            )
        );
    }

    private function stubConfig($eventListener) : array
    {
        return array(
            'apiKey' => 'baz',
            'baseUrl' => Endpoints::TEST_RPC_URL,
            'timeout' => new DateInterval('PT15000S'),
            'eventListener' => $eventListener
        );
    }
}
