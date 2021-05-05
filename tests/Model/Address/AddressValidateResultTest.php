<?php declare(strict_types=1);

namespace Model\Address;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use ShipEngine\Model\Address\AddressValidateResult;
use ShipEngine\Util\ShipEngineSerializer;

/**
 * @covers ShipEngine\Model\Address\AddressValidateResult
 */
final class AddressValidateResultTest extends TestCase
{
    /**
     * @var array
     */
    private static array $successful_address_validate_rpc_response;

    /**
     * @var AddressValidateResult
     */
    private static AddressValidateResult $successful_address_validate_result;

    /**
     *
     */
    public static function setUpBeforeClass(): void
    {
        self::$successful_address_validate_rpc_response = array(
            'jsonrpc' => '2.0',
            'id' => 'req_4aPGmN8gkcWkK6NRa7c5Lo',
            'result' =>
                array(
                    'valid' => true,
                    'address' =>
                        array(
                            'street' =>
                                array(
                                    0 => '4 JERSEY ST',
                                ),
                            'city_locality' => 'BOSTON',
                            'state_province' => 'MA',
                            'postal_code' => '02215',
                            'country_code' => 'US',
                            'residential' => true,
                        ),
                    'messages' =>
                        array(
                            'info' =>
                                array(
                                    0 => 'Duis',
                                    1 => 'voluptate sed sunt',
                                    2 => 'nisi irure amet',
                                    3 => 'dolore aute',
                                    4 => 'exercitation esse aliquip aute est',
                                ),
                            'errors' =>
                                array(
                                    0 => 'aute ea nulla',
                                    1 => 'occaecat consequat consectetur in esse',
                                    2 => 'aliqua sed',
                                ),
                            'warnings' =>
                                array(
                                ),
                        ),
                ),
        );
        self::$successful_address_validate_result = new AddressValidateResult(
            self::$successful_address_validate_rpc_response
        );
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testConstructSuccessResponse(): void
    {
        $this->assertInstanceOf(AddressValidateResult::class, self::$successful_address_validate_result);
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testJsonSerialize(): void
    {
        $json = self::$successful_address_validate_result->jsonSerialize();

        $this->assertNotNull($json);
        $this->assertIsArray($json);
    }
}
