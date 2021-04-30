<?php declare(strict_types=1);

namespace Service;

use PHPUnit\Framework\TestCase;
use ShipEngine\ShipEngineClient;
use ShipEngine\ShipEngineConfig;
use ShipEngine\Util\Constants\RPCMethods;

/**
 * @covers \ShipEngine\ShipEngineClient
 * @covers \ShipEngine\ShipEngineConfig
 * @covers \ShipEngine\Util\Assert
 * @covers \ShipEngine\Util\VersionInfo
 */
final class ShipEngineClientTest extends TestCase
{
    /**
     * @var ShipEngineClient
     */
    private static ShipEngineClient $shipengine_client;

    /**
     *
     */
    public static function setUpBeforeClass(): void
    {
        self::$shipengine_client = new ShipEngineClient();
    }

    /**
     * This is a helper method to test protected or private methods on classes.
     *
     * @param $obj The object that has the protected/private method you wish to test.
     * @param $method_name The name of the protected/private method on the object you wish to access.
     * @param array $args An array of arguments you wish to pass into the target method.
     * @return mixed Returns the result or output of the protected/private method that is being accessed.
     * @throws \ReflectionException In the even that the Reflection fails this exception is thrown.
     */
    protected static function callMethod($obj, $method_name, array $args)
    {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($method_name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }

    /**
     * Tests the private `wrapRequest` method on the ShipEngineClient.
     *
     * @throws ReflectionException
     */
    public function testWrapRequest()
    {
        $wrap_request = $this->callMethod(
            self::$shipengine_client,
            'wrapRequest',
            [
                RPCMethods::ADDRESS_VALIDATE,
                array(
                    'street' => array(
                        '4 Jersey St',
                        'validate-residential-address',
                    ),
                    'city_locality' => 'Boston',
                    'state_province' => 'MA',
                    'postal_code' => '02215',
                    'country_code' => 'US',
                )
            ]
        );

        $params = $wrap_request['params'];
        $this->assertIsArray($wrap_request);
        $this->assertArrayHasKey('id', $wrap_request);
        $this->assertArrayHasKey('jsonrpc', $wrap_request);
        $this->assertArrayHasKey('method', $wrap_request);
        $this->assertArrayHasKey('params', $wrap_request);
        $this->assertIsArray($params);
        $this->assertArrayHasKey('street', $params);
        $this->assertIsArray($params['street']);
        $this->assertArrayHasKey('city_locality', $params);
        $this->assertArrayHasKey('state_province', $params);
        $this->assertArrayHasKey('postal_code', $params);
        $this->assertArrayHasKey('country_code', $params);
    }

//    /**
//     *
//     */
//    public function testRequest()
//    {
//
//    }
//
//    public function testSendRPCRequest()
//    {
//
//    }
//
//    public function testSendRequest()
//    {
//
//    }
//
//    public function testHandleResponse()
//    {
//
//    }
//
//    public function testDeriveUserAgent()
//    {
//
//    }
}
