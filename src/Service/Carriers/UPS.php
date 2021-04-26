<?php declare(strict_types=1);

namespace ShipEngine\Service\Carriers;

use ShipEngine\Util;
use ShipEngine\Service\Carriers\Carriers;

final class UPS
{
    use Util\Getters;

    private string $carrier_name = 'United Parcel Service';

    private string $carrier_code = Carriers::UPS;
}
