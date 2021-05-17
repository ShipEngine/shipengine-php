<?php declare(strict_types=1);

namespace ShipEngine;

use PHPUnit\Framework\TestCase;
use ShipEngine\Message\RateLimitExceededException;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\Message\ValidationException;
use ShipEngine\Model\Address\Address;
use ShipEngine\Util\Constants\Endpoints;
use ShipEngine\Util\Constants\ErrorCode;
use ShipEngine\Util\Constants\ErrorSource;
use ShipEngine\Util\Constants\ErrorType;

/**
 * @covers \ShipEngine\Util\Assert
 * @covers \ShipEngine\ShipEngineConfig
 * @covers \ShipEngine\Message\ShipEngineException
 * @covers \ShipEngine\Message\ValidationException
 * @covers \ShipEngine\ShipEngine
 * @covers \ShipEngine\ShipEngine
 * @covers \ShipEngine\ShipEngineConfig
 */
final class ShipEngineConfigTest extends TestCase
{
    private static ShipEngine $shipengine;

    private static ShipEngineConfig $config;

    private static Address $goodAddress;

    private static string $test_url;

    public static function setUpBeforeClass(): void
    {
        self::$test_url = Endpoints::TEST_RPC_URL;
        self::$config = new ShipEngineConfig(
            array(
                'apiKey' => 'baz',
                'baseUrl' => self::$test_url,
                'pageSize' => 75,
                'retries' => 7,
                'timeout' => new \DateInterval('PT15S'),
                'events' => null
            )
        );
        self::$shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz',
                'baseUrl' => self::$test_url,
                'pageSize' => 75,
                'retries' => 7,
                'timeout' => new \DateInterval('PT15S'),
                'events' => null
            )
        );
        self::$goodAddress = new Address(
            array(
                'street' => array('4 Jersey St', 'ste 200'),
                'cityLocality' => 'Boston',
                'stateProvince' => 'MA',
                'postalCode' => '02215',
                'countryCode' => 'US',
            )
        );
    }

    public function testNoAPIKey(): void
    {
        try {
            new ShipEngineConfig(
                array(
                    'baseUrl' => self::$test_url,
                    'pageSize' => 75,
                    'retries' => 7,
                    'timeout' => new \DateInterval('PT15S'),
                    'events' => null
                )
            );
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $e);
            $this->assertNull($error['requestId']);
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('validation', $error['type']);
            $this->assertEquals('field_value_required', $error['errorCode']);
            $this->assertEquals(
                'A ShipEngine API key must be specified.',
                $error['message']
            );
        }
    }

    public function testEmptyAPIKey(): void
    {
        try {
            new ShipEngineConfig(
                array(
                    'apiKey' => '',
                    'baseUrl' => self::$test_url,
                    'pageSize' => 75,
                    'retries' => 7,
                    'timeout' => new \DateInterval('PT15S'),
                    'events' => null
                )
            );
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $e);
            $this->assertNull($error['requestId']);
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('validation', $error['type']);
            $this->assertEquals('field_value_required', $error['errorCode']);
            $this->assertEquals(
                'A ShipEngine API key must be specified.',
                $error['message']
            );
        }
    }

    public function testInvalidRetries(): void
    {
        try {
            new ShipEngineConfig(
                array(
                    'apiKey' => 'baz',
                    'baseUrl' => self::$test_url,
                    'pageSize' => 75,
                    'retries' => -7,
                    'timeout' => new \DateInterval('PT15S'),
                    'events' => null
                )
            );
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $e);
            $this->assertNull($error['requestId']);
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('validation', $error['type']);
            $this->assertEquals('invalid_field_value', $error['errorCode']);
            $this->assertEquals(
                'Retries must be zero or greater.',
                $error['message']
            );
        }
    }

    public function testInvalidTimeout(): void
    {
        try {
            new ShipEngineConfig(
                array(
                    'apiKey' => 'baz',
                    'baseUrl' => self::$test_url,
                    'pageSize' => 75,
                    'retries' => 7,
                    'timeout' => new \DateInterval('PT0S'),
                    'events' => null
                )
            );
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $e);
            $this->assertNull($error['requestId']);
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('validation', $error['type']);
            $this->assertEquals('invalid_field_value', $error['errorCode']);
            $this->assertEquals(
                'Timeout must be greater than zero.',
                $error['message']
            );
        }
    }

    public function testEmptyAPIKeyInMethodCall(): void
    {
        try {
            self::$shipengine->validateAddress(self::$goodAddress, array('apiKey' => ''));
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $e);
            $this->assertNull($error['requestId']);
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('validation', $error['type']);
            $this->assertEquals('field_value_required', $error['errorCode']);
            $this->assertEquals(
                'A ShipEngine API key must be specified.',
                $error['message']
            );
        }
    }

    public function testInvalidRetriesInMethodCall(): void
    {
        try {
            self::$shipengine->validateAddress(self::$goodAddress, array('retries' => -7));
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $e);
            $this->assertNull($error['requestId']);
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('validation', $error['type']);
            $this->assertEquals('invalid_field_value', $error['errorCode']);
            $this->assertEquals(
                'Retries must be zero or greater.',
                $error['message']
            );
        }
    }

    public function testInvalidTimeoutInMethodCall(): void
    {
        try {
            $di = new \DateInterval('PT7S');
            $di->invert = 1;
            self::$shipengine->validateAddress(self::$goodAddress, array('timeout' => $di));
        } catch (ValidationException $e) {
            $error = $e->jsonSerialize();
            $this->assertInstanceOf(ValidationException::class, $e);
            $this->assertNull($error['requestId']);
            $this->assertEquals('shipengine', $error['source']);
            $this->assertEquals('validation', $error['type']);
            $this->assertEquals('invalid_field_value', $error['errorCode']);
            $this->assertEquals(
                'Timeout must be greater than zero.',
                $error['message']
            );
        }
    }

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(ShipEngineConfig::class, self::$config);
    }

    public function testMergeApiKey(): void
    {
        $config = new ShipEngineConfig(
            array(
                'apiKey' => 'baz',
                'baseUrl' => self::$test_url,
                'pageSize' => 75,
                'retries' => 7,
                'timeout' => new \DateInterval('PT15S'),
                'events' => null
            )
        );
        $update_config = array('apiKey' => 'foo');
        $new_config = $config->merge($update_config);
        $this->assertEquals($update_config['apiKey'], $new_config->apiKey);
    }

    public function testMergeBaseUrl(): void
    {
        $config = new ShipEngineConfig(
            array(
                'apiKey' => 'baz',
                'baseUrl' => self::$test_url,
                'pageSize' => 75,
                'retries' => 7,
                'timeout' => new \DateInterval('PT15S'),
                'events' => null
            )
        );
        $update_config = array('baseUrl' => 'https://google.com/');
        $new_config = $config->merge($update_config);
        $this->assertEquals($update_config['baseUrl'], $new_config->baseUrl);
    }

    public function testMergePageSize(): void
    {
        $config = new ShipEngineConfig(
            array(
                'apiKey' => 'baz',
                'baseUrl' => self::$test_url,
                'pageSize' => 75,
                'retries' => 7,
                'timeout' => new \DateInterval('PT15S'),
                'events' => null
            )
        );
        $update_config = array('pageSize' => 50);
        $new_config = $config->merge($update_config);
        $this->assertEquals($update_config['pageSize'], $new_config->pageSize);
    }

    public function testMergeRetries(): void
    {
        $config = new ShipEngineConfig(
            array(
                'apiKey' => 'baz',
                'baseUrl' => self::$test_url,
                'pageSize' => 75,
                'retries' => 7,
                'timeout' => new \DateInterval('PT15S'),
                'events' => null
            )
        );
        $update_config = array('retries' => 1);
        $new_config = $config->merge($update_config);
        $this->assertEquals($update_config['retries'], $new_config->retries);
    }

    public function testMergeTimeout(): void
    {
        $config = new ShipEngineConfig(
            array(
                'apiKey' => 'baz',
                'baseUrl' => self::$test_url,
                'pageSize' => 75,
                'retries' => 7,
                'timeout' => new \DateInterval('PT15S'),
                'events' => null
            )
        );
        $update_config = array('timeout' => new \DateInterval('PT25S'));
        $new_config = $config->merge($update_config);
        $this->assertEquals($update_config['timeout'], $new_config->timeout);
    }

    public function testConfigWithRetriesDisabled()
    {
        try {
            $config = new ShipEngineConfig(
                array(
                    'apiKey' => 'baz',
                    'baseUrl' => self::$test_url,
                    'pageSize' => 75,
                    'retries' => 0,
                    'timeout' => new \DateInterval('PT15S')
                )
            );
        } catch (ShipEngineException $err) {
            $error = $err->jsonSerialize();
            $this->assertInstanceOf(RateLimitExceededException::class, $err);
            $this->assertNull($error['requestId']);
            $this->assertEquals(ErrorSource::SHIPENGINE, $error['source']);
            $this->assertEquals(ErrorType::VALIDATION, $error['type']);
            $this->assertEquals(ErrorCode::FIELD_VALUE_REQUIRED, $error['errorCode']);
            $this->assertEquals(
                'Invalid address. Either the postal code or the city/locality and state/province must be specified.',
                $error['message']
            );
        }
    }
}
