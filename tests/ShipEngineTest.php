<?php declare(strict_types=1);

namespace ShipEngine;

use PHPUnit\Framework\TestCase;
use ShipEngine\Model\Package\TrackPackageResult;

/**
 * @covers \ShipEngine\ShipEngine
 * @covers \ShipEngine\ShipEngineClient
 * @uses \ShipEngine\ShipEngineConfig
 * @uses \ShipEngine\Util\Assert
 * @uses \ShipEngine\Service\Package\TrackPackageService
 * @uses \ShipEngine\Model\Package\TrackPackageResult
 */
final class ShipEngineTest extends TestCase
{
    private static ShipEngine $shipengine;

    public static function setUpBeforeClass(): void
    {
        self::$shipengine = new ShipEngine(
            array(
                'apiKey' => 'TEST_ycvJAgX6tLB1Awm9WGJmD8mpZ8wXiQ20WhqFowCk32s',
                'baseUrl' => 'https://api.shipengine.com',
                'pageSize' => 75,
                'retries' => 7,
                'timeout' => new \DateInterval('PT15S'),
            )
        );
    }

    public function testInstantiation(): void
    {
        $shipengineInit = new ShipEngine(
            array(
                'apiKey' => 'TEST_ycvJAgX6tLB1Awm9WGJmD8mpZ8wXiQ20WhqFowCk32s',
                'baseUrl' => 'https://api.shipengine.com',
                'pageSize' => 75,
                'retries' => 7,
                'timeout' => new \DateInterval('PT15S'),
            )
        );
        $this->assertInstanceOf(ShipEngine::class, $shipengineInit);
    }

    public function testFetchCarrierAccountsReturnValue(): void
    {
        $carriers = self::$shipengine->listCarriers();

        foreach ($carriers['carriers'] as $carrier) {
            $this->assertEquals($carrier['supports_label_messages'], true);
        }
    }
}
