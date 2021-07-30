<?php declare(strict_types=1);

namespace Util;

// use ShipEngine\Model\Address\Address;
use ShipEngine\Util\ShipEngineSerializer;
use PHPUnit\Framework\TestCase;

/**
 * Class ShipEngineSerializerTest
 *
 * @covers ShipEngine\ShipEngineConfig
 * @covers ShipEngine\Util\Assert
 * @covers ShipEngine\Util\ShipEngineSerializer
 * @covers ShipEngine\ShipEngineConfig::__construct
 * @covers ShipEngine\Util\Assert::isApiKeyValid
 * @covers ShipEngine\Util\Assert::isTimeoutValid
 * @covers ShipEngine\ShipEngineConfig::merge
 * @package Util
 */
final class ShipEngineSerializerTest extends TestCase
{
    public function testSerializeDataToJson(): void
    {
        $serializer = new ShipEngineSerializer();
        $arr = array(
            'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
            'cityLocality' => 'Boston',
            'stateProvince' => 'MA',
            'postalCode' => '02215',
            'countryCode' => 'US',
        );

        $json = $serializer->serializeDataToJson($arr);
        $this->assertJson($json);
    }

    // public function testDeserializeJsonToType()
    // {
    //     $serializer = new ShipEngineSerializer();
    //     $json_string = '{"address": {
    // 		"street": [
    // 			"4 Jersey St",
    // 			"multiple-error-messages"
    // 		],
    // 		"cityLocality": "Boston",
    // 		"stateProvince": "MA",
    // 		"postalCode": "02215",
    // 		"countryCode": "US"
    // 	}}';


    //     $this->assertInstanceOf(
    //         Address::class,
    //         $serializer->deserializeJsonToType($json_string, Address::class)
    //     );
    // }

    // public function testSerializeDataToType()
    // {
    //     $serializer = new ShipEngineSerializer();
    //     $address = array(
    //         'address' => array(
    //             'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
    //             'cityLocality' => 'Boston',
    //             'stateProvince' => 'MA',
    //             'postalCode' => '02215',
    //             'countryCode' => 'US'
    //         )
    //     );

    //     $this->assertInstanceOf(
    //         Address::class,
    //         $serializer->serializeDataToType($address, Address::class)
    //     );
    // }

    public function testInstantiation(): void
    {
        $serializer = new ShipEngineSerializer();

        $this->assertInstanceOf(ShipEngineSerializer::class, $serializer);
    }
}
