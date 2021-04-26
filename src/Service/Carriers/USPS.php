<?php declare(strict_types=1);

namespace ShipEngine\Service\Carriers;

use ShipEngine\Util;
use ShipEngine\Service\Carriers\Carriers;

final class USPS
{
    use Util\Getters;

    private string $carrier_name = 'U.S. Postal Service';

    private string $carrier_code = Carriers::USPS;
}
