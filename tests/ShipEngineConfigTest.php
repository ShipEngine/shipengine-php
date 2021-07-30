<?php declare(strict_types=1);

namespace ShipEngine;

use DateInterval;
use DateTime;
use Mockery;
use PHPUnit\Framework\TestCase;
use ShipEngine\Message\Events\RequestSentEvent;
use ShipEngine\Message\Events\ResponseReceivedEvent;
use ShipEngine\Message\RateLimitExceededException;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\Message\TimeoutException;
use ShipEngine\Message\ValidationException;
use ShipEngine\Util\Constants\Endpoints;

/**
 * @covers \ShipEngine\ShipEngineConfig
 * @uses   \ShipEngine\Message\RateLimitExceededException
 * @uses   \ShipEngine\Util\Assert
 * @uses   \ShipEngine\ShipEngineConfig
 * @uses   \ShipEngine\Message\ShipEngineException
 * @uses   \ShipEngine\Message\ValidationException
 * @uses   \ShipEngine\ShipEngine
 * @uses   \ShipEngine\ShipEngineClient
 * @uses   \ShipEngine\Message\TimeoutException
 */
final class ShipEngineConfigTest extends TestCase
{
    private static ShipEngine $shipengine;

    private static ShipEngineConfig $config;

    // private static Address $goodAddress;

    private static string $test_url;

    public static function setUpBeforeClass(): void
    {
        self::$test_url = Endpoints::TEST_REST_URL;
        self::$config = new ShipEngineConfig(
            array(
                'apiKey' => 'baz',
                'baseUrl' => self::$test_url,
                'pageSize' => 75,
                'retries' => 7,
                'timeout' => new DateInterval('PT15S'),
                'events' => null
            )
        );
        self::$shipengine = new ShipEngine(
            array(
                'apiKey' => 'baz',
                'baseUrl' => self::$test_url,
                'pageSize' => 75,
                'retries' => 7,
                'timeout' => new DateInterval('PT15S'),
            )
        );
        // self::$goodAddress = new Address(
        //     array(
        //         'street' => array('4 Jersey St', 'ste 200'),
        //         'cityLocality' => 'Boston',
        //         'stateProvince' => 'MA',
        //         'postalCode' => '02215',
        //         'countryCode' => 'US',
        //     )
        // );
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testNoAPIKey(): void
    {
        try {
            new ShipEngineConfig(
                array(
                    'baseUrl' => self::$test_url,
                    'pageSize' => 75,
                    'retries' => 7,
                    'timeout' => new DateInterval('PT15S'),
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
                    'timeout' => new DateInterval('PT15S'),
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
                    'timeout' => new DateInterval('PT15S'),
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
                    'timeout' => new DateInterval('PT0S'),
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

    // public function testEmptyAPIKeyInMethodCall(): void
    // {
    //     try {
    //         self::$shipengine->validateAddress(self::$goodAddress, array('apiKey' => ''));
    //     } catch (ValidationException $e) {
    //         $error = $e->jsonSerialize();
    //         $this->assertInstanceOf(ValidationException::class, $e);
    //         $this->assertNull($error['requestId']);
    //         $this->assertEquals('shipengine', $error['source']);
    //         $this->assertEquals('validation', $error['type']);
    //         $this->assertEquals('field_value_required', $error['errorCode']);
    //         $this->assertEquals(
    //             'A ShipEngine API key must be specified.',
    //             $error['message']
    //         );
    //     }
    // }

    // public function testInvalidRetriesInMethodCall(): void
    // {
    //     try {
    //         self::$shipengine->validateAddress(self::$goodAddress, array('retries' => -7));
    //     } catch (ValidationException $e) {
    //         $error = $e->jsonSerialize();
    //         $this->assertInstanceOf(ValidationException::class, $e);
    //         $this->assertNull($error['requestId']);
    //         $this->assertEquals('shipengine', $error['source']);
    //         $this->assertEquals('validation', $error['type']);
    //         $this->assertEquals('invalid_field_value', $error['errorCode']);
    //         $this->assertEquals(
    //             'Retries must be zero or greater.',
    //             $error['message']
    //         );
    //     }
    // }

    // public function testInvalidTimeoutInMethodCall(): void
    // {
    //     try {
    //         $di = new DateInterval('PT7S');
    //         $di->invert = 1;
    //         self::$shipengine->validateAddress(self::$goodAddress, array('timeout' => $di));
    //     } catch (ValidationException $e) {
    //         $error = $e->jsonSerialize();
    //         $this->assertInstanceOf(ValidationException::class, $e);
    //         $this->assertNull($error['requestId']);
    //         $this->assertEquals('shipengine', $error['source']);
    //         $this->assertEquals('validation', $error['type']);
    //         $this->assertEquals('invalid_field_value', $error['errorCode']);
    //         $this->assertEquals(
    //             'Timeout must be greater than zero.',
    //             $error['message']
    //         );
    //     }
    // }

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
                'timeout' => new DateInterval('PT15S'),
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
                'timeout' => new DateInterval('PT15S'),
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
                'timeout' => new DateInterval('PT15S'),
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
                'timeout' => new DateInterval('PT15S'),
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
                'timeout' => new DateInterval('PT15S'),
                'events' => null
            )
        );
        $update_config = array('timeout' => new DateInterval('PT25S'));
        $new_config = $config->merge($update_config);
        $this->assertEquals($update_config['timeout'], $new_config->timeout);
    }

    // public function testConfigWithRetriesDisabled(): void
    // {
    //     try {
    //         $address429 = new Address(
    //             array(
    //                 'street' => array(
    //                     '429 Rate Limit Error'
    //                 ),
    //                 'cityLocality' => 'Boston',
    //                 'stateProvince' => 'MA',
    //                 'postalCode' => '02215',
    //                 'countryCode' => 'US',
    //             )
    //         );
    //         $shipengine = new ShipEngine(
    //             array(
    //                 'apiKey' => 'baz',
    //                 'baseUrl' => self::$test_url,
    //                 'pageSize' => 75,
    //                 'retries' => 0,
    //                 'timeout' => new DateInterval('PT15S'),
    //             )
    //         );
    //         $shipengine->validateAddress($address429);
    //     } catch (ShipEngineException $err) {
    //         $this->assertionsOn429Exception($err, RateLimitExceededException::class);

    //         $eventResult = array();

    //         $this->assertEquals(0, $eventResult[0]->retry);
    //         $this->assertEquals(0, $eventResult[1]->retry);
    //         $this->assertEquals($eventResult[0]->retry, $eventResult[1]->retry);
    //     }
    // }

    // public function testConfigRetryOnceByDefault(): void
    // {
    //     try {
    //         $address429 = new Address(
    //             array(
    //                 'street' => array(
    //                     '429 Rate Limit Error'
    //                 ),
    //                 'cityLocality' => 'Boston',
    //                 'stateProvince' => 'MA',
    //                 'postalCode' => '02215',
    //                 'countryCode' => 'US',
    //             )
    //         );
    //         $shipengine = new ShipEngine(
    //             array(
    //                 'apiKey' => 'baz',
    //                 'baseUrl' => self::$test_url,
    //                 'pageSize' => 75,
    //                 'timeout' => new DateInterval('PT15S'),
    //             )
    //         );
    //         $shipengine->validateAddress($address429);
    //     } catch (ShipEngineException $err) {
    //         $this->assertionsOn429Exception($err, RateLimitExceededException::class);

    //         $requestEventResult = array();
    //         $responseEventResult = array();

    //         $this->assertEquals(0, $requestEventResult[0]->retry);
    //         $this->assertEquals(0, $responseEventResult[0]->retry);

    //         $this->assertEquals(1, $requestEventResult[1]->retry);
    //         $this->assertEquals(1, $responseEventResult[1]->retry);
    //     }
    // }

    // public function testConfigWithCustomRetries(): void
    // {
    //     try {
    //         $address429 = new Address(
    //             array(
    //                 'street' => array(
    //                     '429 Rate Limit Error'
    //                 ),
    //                 'cityLocality' => 'Boston',
    //                 'stateProvince' => 'MA',
    //                 'postalCode' => '02215',
    //                 'countryCode' => 'US',
    //             )
    //         );
    //         $shipengine = new ShipEngine(
    //             array(
    //                 'apiKey' => 'baz',
    //                 'baseUrl' => self::$test_url,
    //                 'pageSize' => 75,
    //                 'retries' => 3,
    //                 'timeout' => new DateInterval('PT15S'),
    //             )
    //         );
    //         $shipengine->validateAddress($address429);
    //     } catch (ShipEngineException $err) {
    //         $this->assertionsOn429Exception($err, RateLimitExceededException::class);

    //         $requestEventResult = array();
    //         $responseEventResult = array();

    //         $this->assertEquals(0, $requestEventResult[0]->retry);
    //         $this->assertEquals(0, $responseEventResult[0]->retry);

    //         $this->assertEquals(1, $requestEventResult[1]->retry);
    //         $this->assertEquals(1, $responseEventResult[1]->retry);

    //         $this->assertEquals(2, $requestEventResult[2]->retry);
    //         $this->assertEquals(2, $responseEventResult[2]->retry);

    //         $this->assertEquals(3, $requestEventResult[3]->retry);
    //         $this->assertEquals(3, $responseEventResult[3]->retry);
    //     }
    // }

    // public function testTimeoutExceptionWhenRetryGreaterThanTimeout(): void
    // {
    //     $config = array(
    //         'apiKey' => 'baz',
    //         'baseUrl' => self::$test_url,
    //         'pageSize' => 75,
    //         'retries' => 0,
    //         'timeout' => new DateInterval('PT2S'),
    //     );

    //     try {
    //         $address429 = new Address(
    //             array(
    //                 'street' => array(
    //                     '429 Rate Limit Error'
    //                 ),
    //                 'cityLocality' => 'Boston',
    //                 'stateProvince' => 'MA',
    //                 'postalCode' => '02215',
    //                 'countryCode' => 'US',
    //             )
    //         );
    //         $shipengine = new ShipEngine($config);
    //         $shipengine->validateAddress($address429);
    //     } catch (ShipEngineException $err) {
    //         $this->assertionsOnTimeoutException($err, $config['timeout']->s);

    //         $eventResult = array();
    //         $spy->shouldHaveReceived('onRequestSent')
    //             ->withArgs(
    //                 function ($event) use (&$eventResult) {
    //                     if ($event instanceof RequestSentEvent) {
    //                         $eventResult[] = $event;
    //                         return true;
    //                     }
    //                     return false;
    //                 }
    //             )->once();

    //         $spy->shouldHaveReceived('onResponseReceived')
    //             ->withArgs(
    //                 function ($event) use (&$eventResult) {
    //                     if ($event instanceof ResponseReceivedEvent) {
    //                         $eventResult[] = $event;
    //                         return true;
    //                     }
    //                     return false;
    //                 }
    //             )->once();
    //         $this->assertEquals(0, $eventResult[0]->retry);
    //         $this->assertEquals(0, $eventResult[1]->retry);
    //         $this->assertEquals($eventResult[0]->retry, $eventResult[1]->retry);
    //         $this->assertEquals(2, $eventResult[0]->timeout->s);
    //     }
    // }

    // public function testConfigRetryWaitsCorrectAmountOfTime(): void
    // {
    //     $testStartTime = new DateTime();
    //     $config = array(
    //         'apiKey' => 'baz',
    //         'baseUrl' => self::$test_url,
    //         'pageSize' => 75,
    //         'retries' => 1,
    //         'timeout' => new DateInterval('PT10S'),
    //     );

    //     try {
    //         $address429 = new Address(
    //             array(
    //                 'street' => array(
    //                     '429 Rate Limit Error'
    //                 ),
    //                 'cityLocality' => 'Boston',
    //                 'stateProvince' => 'MA',
    //                 'postalCode' => '02215',
    //                 'countryCode' => 'US',
    //             )
    //         );
    //         $shipengine = new ShipEngine($config);
    //         $shipengine->validateAddress($address429);
    //     } catch (ShipEngineException $err) {
    //         $this->assertionsOn429Exception($err, RateLimitExceededException::class);

    //         $requestEventResult = array();
    //         $responseEventResult = array();

    //         $this->assertEqualsWithDelta($testStartTime, new DateTime(), 5);
    //         $this->assertEqualsWithDelta($requestEventResult[0]->timestamp, $requestEventResult[1]->timestamp, 5);
    //     }
    // }

    public function assertionsOn429Exception(ShipEngineException $err, string $errorClass): void
    {
        $error = $err->jsonSerialize();
        $this->assertInstanceOf($errorClass, $err);
        $this->assertNotNull($error['requestId']);
        $this->assertStringStartsWith('req_', $error['requestId']);
        $this->assertEquals(ErrorSource::SHIPENGINE, $error['source']);
        $this->assertEquals(ErrorType::SYSTEM, $error['type']);
        $this->assertEquals(ErrorCode::RATE_LIMIT_EXCEEDED, $error['errorCode']);
        $this->assertEquals(
            'You have exceeded the rate limit.',
            $error['message']
        );
        $this->assertNotNull($error['url']);
        $this->assertEquals('https://www.shipengine.com/docs/rate-limits', $error['url']);
    }

    public function assertionsOnTimeoutException(ShipEngineException $err, int $timeout): void
    {
        $error = $err->jsonSerialize();
        $this->assertInstanceOf(TimeoutException::class, $err);
        $this->assertNotNull($error['requestId']);
        $this->assertStringStartsWith('req_', $error['requestId']);
        $this->assertEquals(ErrorSource::SHIPENGINE, $error['source']);
        $this->assertEquals(ErrorType::SYSTEM, $error['type']);
        $this->assertEquals(ErrorCode::TIMEOUT, $error['errorCode']);
        $this->assertEquals(
            "The request took longer than the $timeout seconds allowed.",
            $error['message']
        );
        $this->assertNotNull($error['url']);
        $this->assertEquals('https://www.shipengine.com/docs/rate-limits', $error['url']);
    }

    public function testJsonSerialize(): void
    {
        $se_config = new ShipEngineConfig(
            array(
                'apiKey' => 'baz',
                'baseUrl' => self::$test_url,
                'pageSize' => 75,
                'retries' => 1,
                'timeout' => new DateInterval('PT10S')
            )
        );
        $this->assertJson(json_encode($se_config));
    }
}
