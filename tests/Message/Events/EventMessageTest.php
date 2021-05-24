<?php declare(strict_types=1);

namespace Message\Events;

use PHPUnit\Framework\TestCase;
use ShipEngine\Message\Events\EventMessage;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\Util\Constants\RPCMethods;

/**
 * Class EventMessageTest
 *
 * @covers \ShipEngine\Message\Events\EventMessage
 * @uses \ShipEngine\Message\ShipEngineException
 */
final class EventMessageTest extends TestCase
{
    /**
     * Test the **newEventMessage** method.
     */
    public function testNewEventMessage(): void
    {
        $baseUri = 'https://www.google.com';
        $method = RPCMethods::ADDRESS_VALIDATE;
        $validMessageTypeArray = $this->validMessageTypes();
        $validMessages = $this->validMessages($method, $baseUri);

        foreach ($validMessageTypeArray as $message) {
            $eventMessage = EventMessage::newEventMessage(
                $method,
                $baseUri,
                $message
            );

            $this->assertIsString($eventMessage);
        }

        $this->assertEquals("Calling the ShipEngine $method API at $baseUri", $validMessages[0]);
        $this->assertEquals("Retrying the ShipEngine $method API at $baseUri", $validMessages[1]);
    }

    /**
     * Tests the eventMessage exception case.
     */
    public function testNewEventMessageExceptionCase(): void
    {
        try {
            $eventMessage = EventMessage::newEventMessage(
                RPCMethods::ADDRESS_VALIDATE,
                'https://www.google.com',
                'pizza_ready'
            );
        } catch (ShipEngineException $err) {
            $this->assertInstanceOf(ShipEngineException::class, $err);
        }
    }

    /**
     * A method that constructs the a message in the correct format.
     *
     * @param string $method
     * @param string $baseUri
     * @return string[]
     */
    private function validMessages(string $method, string $baseUri): array
    {
        return array(
            "Calling the ShipEngine $method API at $baseUri",
            "Retrying the ShipEngine $method API at $baseUri"
        );
    }

    /**
     * A method that returns an array of valid **messageTypes**
     *
     * @return string[]
     */
    private function validMessageTypes(): array
    {
        return array('base_message', 'retry_message');
    }
}
