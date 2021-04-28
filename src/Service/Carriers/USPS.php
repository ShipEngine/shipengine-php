<?php declare(strict_types=1);

namespace ShipEngine\Service\Carriers;

use ShipEngine\Util;
use ShipEngine\Service\Carriers\Carriers;

/**
 * Class USPS - Immutable carrier object representing USPS (U.S. Postal Service).
 *
 * @package ShipEngine\Service\Carriers
 */
final class USPS
{
    use Util\Getters;

    /**
     * The common carrier/provider name.
     *
     * @var string
     */
    private string $carrier_name = 'U.S. Postal Service';

    /**
     * The actual **carrier_code** that ShipEngine API uses: `stamps_com`
     *
     * @var string
     */
    private string $carrier_code = Carriers::USPS;
}
