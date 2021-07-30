<?php declare(strict_types=1);

// namespace ShipEngine;

// use PHPUnit\Framework\TestCase;
// use ShipEngine\Util\Constants\RPCMethods;

// /**
//  * @covers \ShipEngine\ShipEngineClient
//  * @covers \ShipEngine\ShipEngineConfig
//  * @covers \ShipEngine\Util\Assert
//  */
// final class ShipEngineClientTest extends TestCase
// {
//     /**
//      * @var ShipEngineClient
//      */
//     private static ShipEngineClient $shipengine_client;

//     /**
//      *
//      */
//     public static function setUpBeforeClass(): void
//     {
//         self::$shipengine_client = new ShipEngineClient();
//     }

//     protected static function callPrivateMethod($obj, $method_name, array $args)
//     {
//         $class = new \ReflectionClass($obj);
//         $method = $class->getMethod($method_name);
//         $method->setAccessible(true);
//         return $method->invokeArgs($obj, $args);
//     }

//     /**
//      * Tests the private `wrapRequest` method on the ShipEngineClient.
//      */
//     public function testWrapRequest(): void
//     {
//         $wrap_request = $this->callPrivateMethod(
//             self::$shipengine_client,
//             'wrapRequest',
//             [
//                 RPCMethods::ADDRESS_VALIDATE,
//                 array(
//                     'street' => array(
//                         '4 Jersey St',
//                         'validate-isResidential-address',
//                     ),
//                     'cityLocality' => 'Boston',
//                     'stateProvince' => 'MA',
//                     'postalCode' => '02215',
//                     'countryCode' => 'US',
//                 )
//             ]
//         );

//         $params = $wrap_request['params'];
//         $this->assertIsArray($wrap_request);
//         $this->assertArrayHasKey('id', $wrap_request);
//         $this->assertArrayHasKey('jsonrpc', $wrap_request);
//         $this->assertArrayHasKey('method', $wrap_request);
//         $this->assertArrayHasKey('params', $wrap_request);
//         $this->assertIsArray($params);
//         $this->assertArrayHasKey('street', $params);
//         $this->assertIsArray($params['street']);
//         $this->assertArrayHasKey('cityLocality', $params);
//         $this->assertArrayHasKey('stateProvince', $params);
//         $this->assertArrayHasKey('postalCode', $params);
//         $this->assertArrayHasKey('countryCode', $params);
//     }

//     public function testWrapRequestWithNullParams(): void
//     {
//         $wrap_request = $this->callPrivateMethod(
//             self::$shipengine_client,
//             'wrapRequest',
//             [
//                 RPCMethods::ADDRESS_VALIDATE,
//                 null
//             ]
//         );

//         $this->assertArrayNotHasKey('params', $wrap_request);
//         $this->assertArrayHasKey('id', $wrap_request);
//         $this->assertArrayHasKey('jsonrpc', $wrap_request);
//         $this->assertArrayHasKey('method', $wrap_request);
//     }
// }
