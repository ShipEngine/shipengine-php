<?php declare(strict_types=1);

namespace ShipEngine\Service\Carriers;

use ShipEngine\Util;
use ShipEngine\Service\Carriers\Carriers;

final class FedEx
{
    use Util\Getters;

    private string $carrier_name = 'FedEx';

    private string $carrier_code = Carriers::FEDEX;
}
