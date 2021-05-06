<?php declare(strict_types=1);

namespace ShipEngine\Message\Events;

use ShipEngine\Model\Address\Address;
use DateInterval;
use PHPUnit\Framework\TestCase;
use ShipEngine\Message\Events\RequestSentEvent;
use ShipEngine\Message\Events\ShipEngineEventListener;
use ShipEngine\ShipEngine;
use ShipEngine\ShipEngineClient;
use ShipEngine\Util\Constants\Endpoints;
use ShipEngine\Util\Constants\RPCMethods;
use ShipEngine\ShipEngineConfig;

class Foo {
    function foo() { return 42; }
    function bar() { return $this->foo(); }
}

/**
 * @covers \ShipEngine\Message\Events\RequestSentEvent
 * 
 * To run in isolation:
 *   ./vendor/bin/phpunit --process-isolation --filter RequestSentEventTest
 * 
 * This test should mimic:
 * https://github.com/ShipEngine/shipengine-js/blob/6363c722569436a69c4ffa7fe5f1c51e17c12e8d/test/specs/events.spec.js#L9-L37
 * 
 */
final class RequestSentEventTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @var ShipEngine
     */
    private static ShipEngine $shipengine;
    /**
     * @var Object
     */
    private static Object $event_listener;

    // public function tearDown() {
    //     \Mockery::close();
    // }

    /**
     *
     */
    public static function setUpBeforeClass(): void
    {
        self::$event_listener = new ShipEngineEventListener;
        self::$shipengine = new ShipEngine(
            array(
                'api_key' => 'baz',
                'base_url' => Endpoints::TEST_RPC_URL,
                'page_size' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15000S'),
                'event_listener' => self::$event_listener
            )
        );
    }

    public function testRequestSent()
    {
        $good_address = new Address(
            array(
                'street' => array('4 Jersey St', 'ste 200'),
                'city_locality' => 'Boston',
                'state_province' => 'MA',
                'postal_code' => '02215',
                'country_code' => 'US',
            )
        );
        
        // NOTE:NW `PHPUnit\Framework\MockObject\ClassIsFinalException:
        // Class "ShipEngine\Message\Events\ShipEngineEventListener" is declared
        //  "final" and cannot be mocked
        // See: http://docs.mockery.io/en/latest/reference/final_methods_classes.html`
        // Solution: Use \Mockery with an instantiated class argument, a.k.a
        // "proxied partial test doubles."
        $foo = \Mockery::mock("Foo");
        // Passes
        $foo->shouldReceive('foo')
        ->once();
        $foo->foo();

        $temp = new Foo;
        $foo = \Mockery::mock($temp);
        // Passes
        $foo->shouldReceive('foo')
        ->once();
        $foo->foo();
        
        # $mock_request = self::$shipengine->validateAddress($good_address);

        $client = new ShipEngineClient();
        $foo = \Mockery::mock($client);
        // Fails, curiously.
        //
        // TODO:NW Wire this up successfully.
        //
        // $foo->shouldReceive('request')
        //->once();
        $api_response = $client->request(
            RPCMethods::ADDRESS_VALIDATE,
            new ShipEngineConfig(array(
                'api_key' => 'baz',
                'base_url' => Endpoints::TEST_RPC_URL,
                'page_size' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT15000S'),
                'event_listener' => self::$event_listener
            )),
            $good_address->jsonSerialize()
        );

        $listener = new ShipEngineEventListener();
        // Ultimately, we want:
        // $spy = \Mockery::spy(self::$event_listener);
        // OR
        // $foo = \Mockery::mock($listener);
        // $foo->shouldReceive('onRequestSent')
        // ->once();
        # $mock_request = self::$shipengine->validateAddress($good_address);

        // assert
        //\Mockery::close();
    }
    
}
