<?php declare(strict_types=1);

namespace ShipEngine\Service\Carriers;

use ShipEngine\Util;
use ShipEngine\Service\Carriers\Carriers;

/**
 * Class FedEx - Immutable carrier object representing FedEx (Federal Express).
 *
 * @package ShipEngine\Service\Carriers
 */
final class FedEx
{
    use Util\Getters;

    /**
     * The common carrier/provider name.
     *
     * @var string
     */
    private string $carrier_name = 'FedEx';

    /**
     * The actual **carrier_code** that ShipEngine API uses: `fedex`
     *
     * @var string
     */
    private string $carrier_code = Carriers::FEDEX;
}
