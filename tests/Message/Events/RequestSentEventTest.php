<?php declare(strict_types=1);

namespace Message\Events;

use DateInterval;
use DateTime;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Http\Client\ClientExceptionInterface;
use ShipEngine\Message\Events\RequestSentEvent;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\Model\Address\Address;
use ShipEngine\ShipEngine;
use ShipEngine\Util\Constants\Endpoints;
use ShipEngine\Util\Constants\RPCMethods;
use ShipEngine\Util\VersionInfo;

/**
 * @covers \ShipEngine\Message\Events\RequestSentEvent
 * @covers \ShipEngine\Message\Events\ResponseReceivedEvent
 * @covers \ShipEngine\Message\Events\ShipEngineEvent
 * @covers \ShipEngine\Message\Events\ShipEngineEventListener
 * @uses   \ShipEngine\Model\Address\Address
 * @uses   \ShipEngine\Model\Address\AddressValidateResult
 * @uses   \ShipEngine\Service\Address\AddressService
 * @uses   \ShipEngine\Message\RateLimitExceededException
 * @uses   \ShipEngine\Message\ShipEngineException
 * @uses   \ShipEngine\ShipEngine
 * @uses   \ShipEngine\ShipEngineClient
 * @uses   \ShipEngine\ShipEngineConfig
 * @uses   \ShipEngine\Util\Assert
 * @uses   \ShipEngine\Util\VersionInfo
 * @uses   \ShipEngine\Message\Events\EventMessage
 * @uses   \ShipEngine\Message\Events\EventOptions
 */
final class RequestSentEventTest extends MockeryTestCase
{
    /**
     * Private instance of Mocker Spy() to be shared across assertions.
     *
     * @var object|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    private object $spy;

    /**
     * Instantiate fixtures that will be shared across test methods.
     */
    public function setUp(): void
    {
        $this->spy = Mockery::spy('ShipEngineEventListener');
    }

    /**
     * A method using **Mockery Spies** to test the **RequestSentEvent**
     * being emitted on successful requests.
     *
     * @throws ClientExceptionInterface
     */
    public function testRequestSentEvent(): void
    {
        $config = $this->testConfig($this->spy, 0);
        $shipengine = new ShipEngine($config);
        $goodAddress = $this->goodAddress();

        $shipengine->validateAddress($goodAddress);

        $eventResult = null;
        $this->spy->shouldHaveReceived('onRequestSent')
            ->withArgs(
                function ($event) use (&$eventResult) {
                    if ($event instanceof RequestSentEvent) {
                        $eventResult = $event;
                        return true;
                    }
                    return false;
                }
            )->once();

        $this->assertRequestEventOnSuccess($eventResult, $config);
    }


    /**
     * A method using **Mockery Spies** to test the **RequestSentEvent**
     * being emitted on retries per **JIRA DX-1551**.
     *
     * @throws ClientExceptionInterface
     */
    public function testRequestSentEventOnRetries(): void
    {
        $config = $this->testConfig($this->spy, 1);
        $shipengine = new ShipEngine($config);

        $eventResult = array();

        try {
            $shipengine->validateAddress($this->get429Response());
        } catch (ShipEngineException $err) {
            $this->spy->shouldHaveReceived('onRequestSent')
                ->withArgs(
                    function ($event) use (&$eventResult) {
                        if ($event instanceof RequestSentEvent) {
                            $eventResult[] = $event;
                            return true;
                        }
                        return false;
                    }
                )->twice();
            $this->assertRequestSentEventOnRetries($eventResult, $config);
        }
    }

    public function testUserAgentInRequestSentEvent(): void
    {
        $config = $this->testConfig($this->spy, 0);
        $shipengine = new ShipEngine($config);

        $eventResult = null;

        $shipengine->validateAddress($this->goodAddress());
        $this->spy->shouldHaveReceived('onRequestSent')
            ->withArgs(
                function ($event) use (&$eventResult) {
                    if ($event instanceof RequestSentEvent) {
                        $eventResult = $event;
                        return true;
                    }
                    return false;
                }
            )->once();

        $requestHeaders = explode('/', $eventResult->headers['User-Agent']);
        $versionNumber = explode(' ', $requestHeaders[1])[0];
        $this->assertEquals(VersionInfo::string(), $versionNumber);
    }

    /**
     * Tests the assertions outlined in **JIRA DX-1550**.
     *
     * @param RequestSentEvent $event
     * @param array $config
     */
    private function assertRequestEventOnSuccess(RequestSentEvent $event, array $config): void
    {
        $this->assertInstanceOf(RequestSentEvent::class, $event);
        $this->assertEqualsWithDelta($event->timestamp, new DateTime(), 5);
        $this->assertEquals($event->type, RequestSentEvent::REQUEST_SENT);
        $this->assertEquals($event->message, $this->expectedMessage($config, 'success'));
        $this->assertEquals($event->url, $config['baseUrl']);
        $this->assertEquals($event->headers['Api-Key'], $config['apiKey']);
        $this->assertEquals($event->headers['Content-Type'], 'application/json');
        $this->assertEquals($event->body['method'], RPCMethods::ADDRESS_VALIDATE);
        $this->assertEquals($event->retry, 0);
        $this->assertEquals($event->timeout, $config['timeout']);
    }

    /**
     * Tests the assertions outlined in **JIRA DX-1551**.
     *
     * @param array $events
     * @param array $config
     */
    private function assertRequestSentEventOnRetries(array $events, array $config): void
    {
        $count = 0;
        [$event1, $event2] = $events;

        $this->assertEquals($event1->message, $this->expectedMessage($config, 'success'));
        $this->assertEquals($event2->message, $this->expectedMessage($config, 'retry'));
        $this->assertEquals(0, $event1->retry);
        $this->assertEquals(1, $event2->retry);
        foreach ([$event1, $event2] as $event) {
            $this->assertInstanceOf(RequestSentEvent::class, $event);
            $this->assertObjectHasAttribute('timestamp', $event);
            $this->assertEquals(RequestSentEvent::REQUEST_SENT, $event->type);
            $this->assertEquals($event->url, $config['baseUrl']);
            $this->assertEquals($event->headers['Api-Key'], $config['apiKey']);
            $this->assertEquals($event->headers['Content-Type'], 'application/json');
            $this->assertEquals($event->body['method'], RPCMethods::ADDRESS_VALIDATE);
            $this->assertEquals($event->timeout, $config['timeout']);
            $this->assertEquals($count, $event->retry);
            $count++;
        }
    }

    /**
     * A method that returns the expected exception message in
     * the **testResponseReceivedEvent()** test.
     *
     * @param array $config
     * @param string $messageType
     * @return string
     */
    private function expectedMessage(array $config, string $messageType): string
    {
        $url = $config['baseUrl'];
        $method = RPCMethods::ADDRESS_VALIDATE;
        $eventMessage = null;

        switch ($messageType) {
            case 'success':
                $eventMessage = "Calling the ShipEngine $method API at $url";
                break;
            case 'retry':
                $eventMessage = "Retrying the ShipEngine $method API at $url";
                break;
        }

        return $eventMessage;
    }

    /**
     * A method that returns a stub address to use in the call to **$shipengine->validateAddress();**
     * in the **testRequestSentEvent()** test.
     *
     * @return Address
     */
    private function goodAddress(): Address
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
     * Fetch a 429 response from the **ShipEngine API**.
     *
     * @return Address
     */
    private function get429Response(): Address
    {
        return new Address(
            array(
                'street' => array('429 Rate Limit Error'),
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
     * @param int $retries
     * @return array
     */
    private function testConfig(object $eventListener, int $retries): array
    {
        return array(
            'apiKey' => 'baz',
            'baseUrl' => Endpoints::TEST_RPC_URL,
            'pageSize' => 75,
            'retries' => $retries,
            'timeout' => new DateInterval('PT15S'),
            'eventListener' => $eventListener
        );
    }
}
