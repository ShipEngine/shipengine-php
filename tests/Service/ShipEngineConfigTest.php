<?php declare(strict_types=1);

namespace Service;

use ShipEngine\Message\ShipEngineValidationError;
use ShipEngine\Model\Address\Address;
use ShipEngine\Service\ShipEngineConfig;
use PHPUnit\Framework\TestCase;
use ShipEngine\ShipEngine;

/**
 * @covers \ShipEngine\Service\ShipEngineConfig
 */
final class ShipEngineConfigTest extends TestCase
{
    private static ShipEngine $shipengine;

    private static Address $good_address;

    private static string $test_url;

    public static function setUpBeforeClass(): void
    {
        self::$test_url = 'https://simengine.herokuapp.com';
        $config = new ShipEngineConfig(
            array(
                'base_url' => self::$test_url,
                'page_size' => 75,
                'retries' => 7,
                'timeout' => 15000,
                'events' => null
            )
        );
        self::$shipengine = new ShipEngine($config);
        self::$good_address = new Address(
            array('4 Jersey St', 'ste 200'),
            'Boston',
            'MA',
            '02215',
            'US',
        );
    }

    public function testNoAPIKey(): void
    {
        try {
            new ShipEngineConfig(
                array(
                    'base_url' => self::$test_url,
                    'page_size' => 75,
                    'retries' => 7,
                    'timeout' => 15000,
                    'events' => null
                )
            );
        } catch (ShipEngineValidationError $e) {
            $error = $e->errorData();
            $this->assertInstanceOf(ShipEngineValidationError::class, $e);
            $this->assertNull($error['request_id']);
            $this->assertEquals('shipengine', $error['error_source']);
            $this->assertEquals('validation', $error['error_type']);
            $this->assertEquals('field_value_required', $error['error_code']);
            $this->assertEquals(
                'A ShipEngine API key must be specified.',
                $error['error_message']
            );
        }
    }

    public function testEmptyAPIKey(): void
    {
        try {
            new ShipEngineConfig(
                array(
                    'api_key' => '',
                    'base_url' => self::$test_url,
                    'page_size' => 75,
                    'retries' => 7,
                    'timeout' => 15000,
                    'events' => null
                )
            );
        } catch (ShipEngineValidationError $e) {
            $error = $e->errorData();
            $this->assertInstanceOf(ShipEngineValidationError::class, $e);
            $this->assertNull($error['request_id']);
            $this->assertEquals('shipengine', $error['error_source']);
            $this->assertEquals('validation', $error['error_type']);
            $this->assertEquals('field_value_required', $error['error_code']);
            $this->assertEquals(
                'A ShipEngine API key must be specified.',
                $error['error_message']
            );
        }
    }

    public function testInvalidRetries(): void
    {
        try {
            new ShipEngineConfig(
                array(
                    'api_key' => 'baz',
                    'base_url' => self::$test_url,
                    'page_size' => 75,
                    'retries' => -7,
                    'timeout' => 15000,
                    'events' => null
                )
            );
        } catch (ShipEngineValidationError $e) {
            $error = $e->errorData();
            $this->assertInstanceOf(ShipEngineValidationError::class, $e);
            $this->assertNull($error['request_id']);
            $this->assertEquals('shipengine', $error['error_source']);
            $this->assertEquals('validation', $error['error_type']);
            $this->assertEquals('invalid_field_value', $error['error_code']);
            $this->assertEquals(
                'Retries must be zero or greater.',
                $error['error_message']
            );
        }
    }

    public function testInvalidTimeout(): void
    {
        try {
            new ShipEngineConfig(
                array(
                    'api_key' => 'baz',
                    'base_url' => self::$test_url,
                    'page_size' => 75,
                    'retries' => 7,
                    'timeout' => 0,
                    'events' => null
                )
            );
        } catch (ShipEngineValidationError $e) {
            $error = $e->errorData();
            $this->assertInstanceOf(ShipEngineValidationError::class, $e);
            $this->assertNull($error['request_id']);
            $this->assertEquals('shipengine', $error['error_source']);
            $this->assertEquals('validation', $error['error_type']);
            $this->assertEquals('invalid_field_value', $error['error_code']);
            $this->assertEquals(
                'Timeout must be greater than zero.',
                $error['error_message']
            );
        }
    }

//    public function testEmptyAPIKeyInMethodCall()
//    {
//        try {
//            self::$shipengine->validateAddress(self::$good_address, array('api_key' => ''));
//        } catch (ShipEngineValidationError $e) {
//            $error = $e->errorData();
//            $this->assertInstanceOf(ShipEngineValidationError::class, $e);
//            $this->assertNull($error['request_id']);
//            $this->assertEquals('shipengine', $error['error_source']);
//            $this->assertEquals('validation', $error['error_type']);
//            $this->assertEquals('field_value_required', $error['error_code']);
//            $this->assertEquals(
//                'A ShipEngine API key must be specified.',
//                $error['error_message']
//            );
//        }
//    }
}
