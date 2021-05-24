<?php declare(strict_types=1);

namespace Model\Carriers;

use ShipEngine\Message\InvalidFieldValueException;
use ShipEngine\Model\Carriers\Carrier;
use PHPUnit\Framework\TestCase;

/**
 * Class CarrierTest
 *
 * @covers \ShipEngine\Model\Carriers\Carrier
 * @uses \ShipEngine\Util\Constants\Carriers
 * @uses \ShipEngine\Util\Constants\CarrierNames
 * @uses \ShipEngine\Message\ShipEngineException
 * @uses \ShipEngine\Message\InvalidFieldValueException
 */
final class CarrierTest extends TestCase
{
    /**
     * Tests the successful instantiation of the **Carrier** object.
     */
    public function testInstantiation(): void
    {
        $carrier = new Carrier('fedex');
        $this->assertInstanceOf(Carrier::class, $carrier);
    }

    /**
     * Tests a failed instantiation of the **Carrier** object.
     */
    public function testInstantiationExceptionCase(): void
    {
        try {
            new Carrier('pizza');
        } catch (InvalidFieldValueException $err) {
            $this->assertInstanceOf(InvalidFieldValueException::class, $err);
        }
    }

    /**
     * Tests the **jsonSerialize** method on the **Carrier** object.
     */
    public function testJsonSerialize(): void
    {
        $carrier = new Carrier('ups');
        $this->assertJson(json_encode($carrier));
    }
}
