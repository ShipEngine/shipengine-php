<?php declare(strict_types=1);

namespace ShipEngine\Service;

use ShipEngine\Models\Address;
use ShipEngine\Models\AddressQuery;
use ShipEngine\Models\AddressQueryResult;

final class AddressesService extends AbstractService
{
    public function query(AddressQuery $address_query): AddressQueryResult
    {
    }

    public function validate(AddressQuery $address_query): bool
    {
    }

    public function normalize(AddressQuery $address_query): Address
    {
    }
}
