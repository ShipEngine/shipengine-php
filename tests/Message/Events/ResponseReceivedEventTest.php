<?php declare(strict_types=1);

namespace Message\Events;

use DateInterval;
use DateTime;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Http\Client\ClientExceptionInterface;
use ShipEngine\Message\Events\ResponseReceivedEvent;
use ShipEngine\Model\Address\Address;
use ShipEngine\ShipEngine;
use ShipEngine\Util\Constants\Endpoints;
use ShipEngine\Util\Constants\RPCMethods;

/**
 * @covers \ShipEngine\Message\Events\ResponseReceivedEvent
 * @covers \ShipEngine\Message\Events\RequestSentEvent
 * @covers \ShipEngine\Message\Events\ShipEngineEventListener
 * @covers \ShipEngine\Message\Events\ShipEngineEvent
 * @uses   \ShipEngine\Model\Address\Address
 * @uses   \ShipEngine\Model\Address\AddressValidateResult
 * @uses   \ShipEngine\Service\Address\AddressService
 * @uses   \ShipEngine\ShipEngine
 * @uses   \ShipEngine\ShipEngineClient
 * @uses   \ShipEngine\ShipEngineConfig
 * @uses   \ShipEngine\Util\Assert
 * @uses   \ShipEngine\Util\VersionInfo
 */
final class ResponseReceivedEventTest extends MockeryTestCase
{
    /**
     * A method using **Mockery Spies** to test the **RequestSentEvent**
     * being emitted.
     *
     * @throws ClientExceptionInterface
     */
    public function testResponseReceivedEvent(): void
    {
        $testStartTime = new DateTime();
        $spy = Mockery::spy('ShipEngineEventListener');
        $config = $this->testConfig($spy);
        $shipengine = new ShipEngine($config);

        $shipengine->validateAddress($this->testAddress());

        $eventResult = null;
        $spy->shouldHaveReceived('onResponseReceived')
            ->withArgs(
                function ($event) use (&$eventResult) {
                    $eventResult = $event;
                    return true;
                }
            )->once();
        $this->assertResponseReceivedEvent($eventResult, $testStartTime, $config);
    }

    /**
     * Tests the assertions outlined in JIRA DX-1552.
     *
     * @param ResponseReceivedEvent $event
     * @param DateTime $testStartTime
     * @param array $config
     */
    private function assertResponseReceivedEvent(
        ResponseReceivedEvent $event,
        DateTime $testStartTime,
        array $config
    ): void {
        $contentTypeHeaders = explode(';', $event->headers['Content-Type'][0]);

        $this->assertInstanceOf(ResponseReceivedEvent::class, $event);
        $this->assertEqualsWithDelta($event->timestamp, new DateTime(), 5);
        $this->assertEquals(ResponseReceivedEvent::RESPONSE_RECEIVED, $event->type);
        $this->assertEquals($this->expectedMessage(), $event->message);
        $this->assertEquals('200', $event->statusCode);
        $this->assertEquals($config['baseUrl'], $event->url);
        $this->assertEquals('application/json', $contentTypeHeaders[0]);
        $this->assertEquals(0, $event->retry);
        $this->assertGreaterThan(0, $event->elapsed->f);
        $this->assertLessThan((new DateTime())->diff($testStartTime)->f, $event->elapsed->f);
    }

    /**
     * A method that returns the expected exception message in
     * the **testResponseReceivedEvent()** test.
     *
     * @return string
     */
    private function expectedMessage(): string
    {
        $method = RPCMethods::ADDRESS_VALIDATE;
        return "Received an HTTP 200 response from the ShipEngine $method API";
    }

    /**
     * A method that returns a stub address to use in the call to **$shipengine->validateAddress();**
     * in the **testResponseReceivedEvent()** test.
     *
     * @return Address
     */
    private function testAddress(): Address
    {
        return new Address(
            array(
                'street' => array('4 Jersey St', 'ste 200'),
                'cityLocality' => 'Boston',
                'stateProvince' => 'MA',
                'postalCode' => '02215',
                'countryCode' => 'US',
            )
        );
    }

    /**
     * A method that returns a stub config object to use when instantiating the **ShipEngine** class
     * in the **testResponseReceivedEvent()** test.
     *
     * @param object $eventListener
     * @return array
     */
    private function testConfig(object $eventListener): array
    {
        return array(
            'apiKey' => 'baz',
            'baseUrl' => Endpoints::TEST_RPC_URL,
            'pageSize' => 75,
            'retries' => 2,
            'timeout' => new DateInterval('PT15000S'),
            'eventListener' => $eventListener
        );
    }
}
