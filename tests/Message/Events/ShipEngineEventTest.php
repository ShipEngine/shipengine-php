<?php declare(strict_types=1);

namespace Message\Events;

use DateInterval;
use ShipEngine\Message\Events\ShipEngineEvent;
use PHPUnit\Framework\TestCase;
use ShipEngine\Message\InvalidFieldValueException;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\ShipEngineConfig;
use ShipEngine\Util\Constants\Endpoints;

/**
 * Class ShipEngineEventTest
 *
 * @covers \ShipEngine\Message\Events\ShipEngineEvent
 * @uses \ShipEngine\ShipEngineConfig
 * @uses \ShipEngine\Message\ShipEngineException
 * @uses \ShipEngine\Message\InvalidFieldValueException
 * @uses \ShipEngine\Util\Assert
 */
final class ShipEngineEventTest extends TestCase
{
    /**
     * Test the the **ShipEngineEvent** throws an exception if an event that does
     * not exist passed into the **emitEvent()** method.
     */
    public function testEmitEvent()
    {
        try {
            $config = new ShipEngineConfig(
                array(
<<<<<<< Updated upstream
                    'apiKey' => 'baz',
                    'baseUrl' => Endpoints::TEST_RPC_URL,
=======
                    'apiKey' => 'baz_sim',
                    'baseUrl' => Endpoints::TEST_REST_URL,
>>>>>>> Stashed changes
                    'pageSize' => 75,
                    'retries' => 1,
                    'timeout' => new DateInterval('PT15S')
                )
            );
            ShipEngineEvent::emitEvent(
                'pizzaIsReady',
                'large pepperoni/sausage with mushrooms',
                $config
            );
        } catch (ShipEngineException $err) {
            $this->assertInstanceOf(InvalidFieldValueException::class, $err);
        }
    }
}
