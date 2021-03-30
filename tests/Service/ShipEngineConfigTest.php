<?php declare(strict_types=1);

namespace Service;

use ShipEngine\Message\ShipEngineValidationError;
use ShipEngine\Service\ShipEngineConfig;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ShipEngine\Service\ShipEngineConfig
 */
final class ShipEngineConfigTest extends TestCase
{
    private static string $test_url;

    public static function setUpBeforeClass(): void
    {
        self::$test_url = 'https://simengine.herokuapp.com';
    }

    public function testNoAPIKey()
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
}
