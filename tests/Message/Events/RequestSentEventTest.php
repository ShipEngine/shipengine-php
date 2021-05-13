<?php declare(strict_types=1);

namespace Message\Events;

use DateInterval;
use DateTime;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Http\Client\ClientExceptionInterface;
use ShipEngine\Model\Address\Address;
use ShipEngine\ShipEngine;
use ShipEngine\Util\Constants\Endpoints;
use ShipEngine\Util\Constants\RPCMethods;

/**
 * @covers \ShipEngine\Message\Events\RequestSentEvent
 * @covers \ShipEngine\Message\Events\ResponseReceivedEvent
 * @covers \ShipEngine\Message\Events\ShipEngineEvent
 * @covers \ShipEngine\Message\Events\ShipEngineEventListener
 * @uses   \ShipEngine\Model\Address\Address
 * @uses   \ShipEngine\Model\Address\AddressValidateResult
 * @uses   \ShipEngine\Service\Address\AddressService
 * @uses   \ShipEngine\ShipEngine
 * @uses   \ShipEngine\ShipEngineClient
 * @uses   \ShipEngine\ShipEngineConfig
 * @uses   \ShipEngine\Util\Assert
 * @uses   \ShipEngine\Util\VersionInfo
 */
final class RequestSentEventTest extends MockeryTestCase
{
    /**
     * A method using **Mockery Spies** to test the **RequestSentEvent**
     * being emitted.
     *
     * @throws ClientExceptionInterface
     */
    public function testRequestSentEvent(): void
    {
        $spy = Mockery::spy('ShipEngineEventListener');
        $config = $this->stubConfig($spy);
        $shipengine = new ShipEngine($config);
        $goodAddress = $this->stubAddress();

        $shipengine->validateAddress($goodAddress);

        $eventResult = null;
        $spy->shouldHaveReceived('onRequestSent')
            ->withArgs(
                function ($event) use (&$eventResult) {
                    $eventResult = $event;
                    return true;
                }
            )->once();

        $this->assertRequestEvent($eventResult, $config);
    }

    /**
     * Tests the assertions outlined in JIRA DX-1550.
     *
     * @param RequestSentEvent $event
     * @param array $config
     */
    private function assertRequestEvent(RequestSentEvent $event, array $config): void
    {
        $this->assertInstanceOf(RequestSentEvent::class, $event);
        $this->assertEqualsWithDelta($event->timestamp, new DateTime(), 5);
        $this->assertEquals($event->type, RequestSentEvent::REQUEST_SENT);
        $this->assertEquals($event->message, $this->expectedMessage($config));
        $this->assertEquals($event->url, $config['baseUrl']);
        $this->assertEquals($event->headers['Api-Key'], $config['apiKey']);
        $this->assertEquals($event->headers['Content-Type'], 'application/json');
        $this->assertEquals($event->body['method'], RPCMethods::ADDRESS_VALIDATE);
        $this->assertEquals($event->retry, 0);
        $this->assertEquals($event->timeout, $config['timeout']);
    }

    /**
     * A method that returns the expected exception message in
     * the **testResponseReceivedEvent()** test.
     *
     * @param $config
     * @return string
     */
    private function expectedMessage($config): string
    {
        $url = $config['baseUrl'];
        $method = RPCMethods::ADDRESS_VALIDATE;
        return "Calling the ShipEngine $method API at $url";
    }

    /**
     * A method that returns a stub address to use in the call to **$shipengine->validateAddress();**
     * in the **testRequestSentEvent()** test.
     *
     * @return Address
     */
    private function stubAddress(): Address
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

    /**
     * A method that returns a stub config object to use when instantiating the **ShipEngine** class
     * in the **testRequestSentEvent()** test.
     *
     * @param object $eventListener
     * @return array
     */
    private function stubConfig(object $eventListener): array
    {
        return array(
            'apiKey' => 'baz',
            'baseUrl' => Endpoints::TEST_RPC_URL,
            'timeout' => new DateInterval('PT15000S'),
            'eventListener' => $eventListener
        );
    }
}
