<?php declare(strict_types=1);

namespace Util;

use ShipEngine\Model\Address\Address;
use ShipEngine\Util\ShipEngineSerializer;
use PHPUnit\Framework\TestCase;

/**
 * Class ShipEngineSerializerTest
 *
 * @covers \ShipEngine\Util\Assert
 * @covers \ShipEngine\Util\ShipEngineSerializer
 * @covers \ShipEngine\Model\Address\Address
 * @package Util
 */
final class ShipEngineSerializerTest extends TestCase
{
    private static ShipEngineSerializer $serializer;

    public static function setUpBeforeClass(): void
    {
        self::$serializer = new ShipEngineSerializer();
    }

    public function testSerializeDataToJson(): void
    {
        $arr = array(
            array('4 Jersey St', 'Ste 200', '2nd Floor'),
            'Boston',
            'MA',
            '02215',
            'US',
        );

        $json = self::$serializer->serializeDataToJson($arr);
        $this->assertJson($json);
    }

    public function testDeserializeJsonToType()
    {
        $json_string = '{
            "name":"kasey",
            "phone":"1234567891",
            "street":[
                "124 Conch St",
                "validate-with-error"
            ],
            "city_locality":"Bikini Bottom",
            "state_province":"Pacific Ocean",
            "postal_code":"4A6 G67",
            "country_code":"US",
            "residential":null
            }';


        $this->assertInstanceOf(
            Address::class,
            self::$serializer->deserializeJsonToType($json_string, Address::class)
        );
    }

    public function testSerializeDataToType()
    {
        $address = (object)array(
            'street' => array('4 Jersey St', 'Ste 200', '2nd Floor'),
            'city_locality' => 'Boston',
            'state_province' => 'MA',
            'postal_code' => '02215',
            'country_code' => 'US'
        );

        $this->assertInstanceOf(
            Address::class,
            self::$serializer->serializeDataToType($address, Address::class)
        );
    }

    public function testInstantiation(): void
    {
        $serializer = new ShipEngineSerializer();

        $this->assertInstanceOf(ShipEngineSerializer::class, $serializer);
    }
}
