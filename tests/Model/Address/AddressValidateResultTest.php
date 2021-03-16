<?php declare(strict_types=1);

namespace Model\Address;

use PHPUnit\Framework\TestCase;
use ShipEngine\Model\Address\AddressValidateResult;
use ShipEngine\Util\ShipEngineSerializer;

/**
 * @covers ShipEngine\Model\Address\AddressValidateResult
 */
final class AddressValidateResultTest extends TestCase
{
    /**
     * @var ShipEngineSerializer
     */
    private static ShipEngineSerializer $serializer;

    /**
     * @var string
     */
    private static string $successful_address_validate_rpc_response;

    /**
     * @var AddressValidateResult|mixed
     */
    private static AddressValidateResult $successful_address_validate_result;

    /**
     *
     */
    public static function setUpBeforeClass(): void
    {
        exec('hoverctl import simengine/rpc/rpc.json');

        self::$serializer = new ShipEngineSerializer();
        self::$successful_address_validate_rpc_response = json_encode(array(
            'valid' => true,
            'messages' =>
                array(
                    'errors' =>
                        array(
                            0 => 'aute ea nulla',
                            1 => 'occaecat consequat consectetur in esse',
                            2 => 'aliqua sed',
                        ),
                    'info' =>
                        array(
                            0 => 'Duis',
                            1 => 'voluptate sed sunt',
                            2 => 'nisi irure amet',
                            3 => 'dolore aute',
                            4 => 'exercitation esse aliquip aute est',
                        ),
                ),
            'address' =>
                array(
                    'street' =>
                        array(
                            0 => 'in nostrud consequat nisi',
                        ),
                    'country_code' => 'BK',
                    'postal_code' => 'ullamco culpa',
                    'city_locality' => 'aliqua',
                    'residential' => false,
                ),
        ), JSON_PRETTY_PRINT);
        self::$successful_address_validate_result = self::$serializer->deserializeJsonToType(
            self::$successful_address_validate_rpc_response,
            AddressValidateResult::class
        );

        // TODO: Add error test cases.
    }

    /**
     * Delete `simengine/rpc/rpc.json` from *Hoverfly*.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        exec('hoverctl delete --force simengine/rpc/rpc.json');
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testConstructSuccessResponse(): void
    {
        $this->assertInstanceOf(AddressValidateResult::class, self::$successful_address_validate_result);
    }

//    public function testConstructValidationFailure()
//    {
//
//    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testJsonSerialize(): void
    {
        $json = self::$successful_address_validate_result->jsonSerialize();

        $this->assertNotNull($json);
        $this->assertIsString($json);
    }
}
