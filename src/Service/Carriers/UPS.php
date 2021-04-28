<?php declare(strict_types=1);

namespace ShipEngine\Service\Carriers;

use ShipEngine\Util;
use ShipEngine\Service\Carriers\Carriers;

/**
 * Class UPS - Immutable carrier object representing UPS (United Parcel Service).
 *
 * @package ShipEngine\Service\Carriers
 */
final class UPS
{
    use Util\Getters;

    /**
     * The common carrier/provider name.
     *
     * @var string
     */
    private string $carrier_name = 'United Parcel Service';

    /**
     * The actual **carrier_code** that ShipEngine API uses: `ups`
     *
     * @var string
     */
    private string $carrier_code = Carriers::UPS;
}
