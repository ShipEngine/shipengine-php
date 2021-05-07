<?php declare(strict_types=1);

namespace ShipEngine\Message\Events;

use DateInterval;
use \Mockery\Adapter\Phpunit\MockeryTestCase;
use ShipEngine\Message\Events\RequestSentEvent;
use ShipEngine\Message\Events\ShipEngineEventListener;
use ShipEngine\Model\Address\Address;
use ShipEngine\ShipEngine;
use ShipEngine\Util\Constants\Endpoints;
use ShipEngine\Util\Constants\RPCMethods;
use ShipEngine\ShipEngineConfig;

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
final class RequestSentEventTest extends MockeryTestCase
{
    public function testRequestSent(): void
    {
        // PHPUnit cannot mock final classes, so we'll use Mockery, which
        // will create a "proxied partial test double."
        // See: http://docs.mockery.io/en/latest/reference/final_methods_classes.html
        $spy = \Mockery::spy('ShipEngineEventListener');
        
        $shipengine = new ShipEngine(
            array(
                'api_key' => 'baz',
                'base_url' => Endpoints::TEST_RPC_URL,
                'timeout' => new DateInterval('PT15000S'),
                'event_listener' => $spy
            )
        );

        $good_address = new Address(
            array(
                'street' => array('11222 Washington Pl'),
                'city_locality' => 'Culver City',
                'state_province' => 'CA',
                'postal_code' => '90230',
                'country_code' => 'US',
            )
        );
    
        $shipengine->validateAddress($good_address);

        $spy->shouldHaveReceived('onRequestSent')->once();
        $spy->shouldNotReceive('arbitraryValue');
    }
}
