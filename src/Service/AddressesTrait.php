<?php declare(strict_types=1);

namespace ShipEngine\Service;

use ShipEngine\Model\Address\Address;
use ShipEngine\Model\Address\Query;
use ShipEngine\Model\Address\QueryResult;

/**
 * Provides convenience methods onto \ShipEngine\Service\AddressesService.
 */
trait AddressesTrait
{

    /**
     * @see \ShipEngine\Service\AddressesService::query().
     */
    public function queryAddress(): QueryResult
    {
        if (func_num_args() > 1) {
            $query = new Query(...func_get_args());
            return $this->addresses->query($query);
        } else {
            return $this->addresses->query(func_get_arg(0));
        }
    }

    /**
     * @see \ShipEngine\Service\AddressesService::validate().
     */
    public function validateAddress(): Bool
    {
        if (func_num_args() > 1) {
            $query = new Query(...func_get_args());
            return $this->addresses->validate($query);
        } else {
            return $this->addresses->validate(func_get_arg(0));
        }
    }

    /**
     * @see \ShipEngine\Service\AddressesService::normalize().
     */
    public function normalizeAddress(): Address
    {
        if (func_num_args() > 1) {
            $query = new Query(...func_get_args());
            return $this->addresses->normalize($query);
        } else {
            return $this->addresses->normalize(func_get_arg(0));
        }
    }
}
