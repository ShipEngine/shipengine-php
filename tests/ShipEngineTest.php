<?php declare(strict_types=1);

namespace ShipEngine\Tests;

use PHPUnit\Framework\TestCase;
use ShipEngine\Model\Address\Address;
use ShipEngine\ShipEngine;

/**
 * @covers \ShipEngine\ShipEngine
 * @covers \ShipEngine\ShipEngineClient
 * @covers \ShipEngine\Service\ServiceFactory
 */
final class ShipEngineTest extends TestCase
{
    private static ShipEngine $shipengine;

    private static Address $good_address;

    public static function setUpBeforeClass(): void
    {
        self::$shipengine = new ShipEngine(
            array(
                'api_key' => 'baz',
                'base_url' => 'https://api.shipengine.com',
                'page_size' => 75,
                'retries' => 7,
                'timeout' => 15000,
                'events' => null
            )
        );
        self::$good_address = new Address(
            array('4 Jersey St', 'ste 200'),
            'Boston',
            'MA',
            '02215',
            'US',
        );
    }

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(ShipEngine::class, self::$shipengine);
    }

//    public function testUserAgentVersionNumber()
//    {
//        $user_agent = self::$shipengine::VERSION;
//
//        $this->assertEquals($user_agent, '');
//    }
}
