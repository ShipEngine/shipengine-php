<?php declare(strict_types=1);

namespace Message\Events;

use DateInterval;
use DateTime;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Http\Client\ClientExceptionInterface;
use ShipEngine\Message\Events\ResponseReceivedEvent;
use ShipEngine\Message\ShipEngineException;
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
 * @uses   \ShipEngine\Message\RateLimitExceededException
 * @uses   \ShipEngine\Message\ShipEngineException
 * @uses   \ShipEngine\ShipEngine
 * @uses   \ShipEngine\ShipEngineClient
 * @uses   \ShipEngine\ShipEngineConfig
 * @uses   \ShipEngine\Util\Assert
 * @uses   \ShipEngine\Message\Events\EventMessage
 * @uses   \ShipEngine\Message\Events\EventOptions
 */
final class ResponseReceivedEventTest extends MockeryTestCase
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
     * After each class this method runs and closes all mocks and verifies all mocks in the global container.
     * It also manages resetting the container static variable to null.
     */
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * A method using **Mockery Spies** to test the **ResponseReceivedEvent**
     * being emitted per **JIRA DX-1550**.
     *
     * @throws ClientExceptionInterface
     */
    public function testResponseReceivedEvent(): void
    {
        $testStartTime = new DateTime();
        $config = $this->testConfig($this->spy, 0);
        $shipengine = new ShipEngine($config);

        $shipengine->validateAddress($this->testAddress());

        $eventResult = null;
        $this->spy->shouldHaveReceived('onResponseReceived')
            ->withArgs(
                function ($event) use (&$eventResult) {
                    if ($event instanceof ResponseReceivedEvent) {
                        $eventResult = $event;
                        return true;
                    }
                    return false;
                }
            )->once();
        $this->assertResponseReceivedEvent($eventResult, $testStartTime, $config, '200', 0);
    }

    /**
     * A method using **Mockery Spies** to test the **ResponseReceivedEvent** being
     * emitted on request/client errors per **JIRA DX-1553**.
     *
     * @throws ClientExceptionInterface
     */
    public function testResponseReceivedOnError(): void
    {
        $testStartTime = new DateTime();
        $config = $this->testConfig($this->spy, 1);
        $shipengine = new ShipEngine($config);

        $eventResult = array();

        try {
            $shipengine->validateAddress($this->get429Response());
        } catch (ShipEngineException $err) {
            $this->spy->shouldHaveReceived('onResponseReceived')
                ->withArgs(
                    function ($event) use (&$eventResult) {
                        if ($event instanceof ResponseReceivedEvent) {
                            $eventResult[] = $event;
                            return true;
                        }
                        return false;
                    }
                )->twice();
            $this->assertResponseReceivedEvent($eventResult[0], $testStartTime, $config, '429', 0);
            $this->assertResponseReceivedEvent($eventResult[1], $testStartTime, $config, '429', 1);
        }
    }

    /**
     * Tests the assertions outlined in **JIRA DX-1552**.
     *
     * @param ResponseReceivedEvent $event
     * @param DateTime $testStartTime
     * @param array $config
     * @param string $statusCode
     * @param int $retries
     */
    private function assertResponseReceivedEvent(
        ResponseReceivedEvent $event,
        DateTime $testStartTime,
        array $config,
        string $statusCode,
        int $retries
    ): void {
        $contentTypeHeaders = explode(';', $event->headers['Content-Type'][0]);

        $this->assertInstanceOf(ResponseReceivedEvent::class, $event);
        $this->assertEqualsWithDelta($event->timestamp, new DateTime(), 5);
        $this->assertEquals(ResponseReceivedEvent::RESPONSE_RECEIVED, $event->type);
        $this->assertEquals($this->expectedMessage($statusCode), $event->message);
        $this->assertEquals($statusCode, $event->statusCode);
        $this->assertEquals($config['baseUrl'], $event->url);
        $this->assertEquals('application/json', $contentTypeHeaders[0]);
        $this->assertEquals($retries, $event->retry);
        $this->assertGreaterThan(0, $event->elapsed->f);
        $this->assertLessThan((new DateTime())->diff($testStartTime)->f, $event->elapsed->f);
    }

    /**
     * A method that returns the expected exception message in
     * the **testResponseReceivedEvent()** test.
     *
     * @param string $statusCode
     * @return string
     */
    private function expectedMessage(string $statusCode): string
    {
        $method = RPCMethods::ADDRESS_VALIDATE;
        return "Received an HTTP $statusCode response from the ShipEngine $method API";
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
     * Fetch a 429 response from the simegnine.
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
     * in the **testResponseReceivedEvent()** test.
     *
     * @param object $eventListener
     * @param int $retries
     * @return array
     */
    private function testConfig(object $eventListener, int $retries): array
    {
        return array(
<<<<<<< Updated upstream
            'apiKey' => 'baz',
            'baseUrl' => Endpoints::TEST_RPC_URL,
=======
            'apiKey' => 'baz_sim',
            'baseUrl' => Endpoints::TEST_REST_URL,
>>>>>>> Stashed changes
            'pageSize' => 75,
            'retries' => $retries,
            'timeout' => new DateInterval('PT15S'),
            'eventListener' => $eventListener
        );
    }

    /**
     * Test the jsonSerialize method on the the RequestSentEvent.
     */
    public function testJsonSerialize(): void
    {
        $responseReceivedEvent = new ResponseReceivedEvent(
            'testing the request sent event.',
            'req_h08s7fe7h3f4fhq4fw4f5',
            'https://google.com',
            200,
            array(),
            array(),
            300,
            new DateInterval('PT3S')
        );

        $this->assertJson(json_encode($responseReceivedEvent));
    }
}
