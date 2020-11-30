<?php declare(strict_types=1);

namespace ShipEngine\Service;

use ShipEngine\Model\Address;
use ShipEngine\Model\AddressQuery;
use ShipEngine\Model\AddressQueryResult;

trait AddressesTrait
{

    /**
     *
     */
    public function queryAddress(): AddressQueryResult
    {
        if (func_num_args() > 1) {
            $query = new AddressQuery(...func_get_args());
            return $this->addresses->query($query);
        } else {
            return $this->addresses->query(func_get_arg(0));
        }
    }

    /**
     *
     */
    public function validateAddress(): Bool
    {
        if (func_num_args() > 1) {
            $query = new AddressQuery(...func_get_args());
            return $this->addresses->validate($query);
        } else {
            return $this->addresses->validate(func_get_arg(0));
        }
    }

    /**
     *
     */
    public function normalizeAddress(): Address
    {
        if (func_num_args() > 1) {
            $query = new AddressQuery(...func_get_args());
            return $this->addresses->normalize($query);
        } else {
            return $this->addresses->normalize(func_get_arg(0));
        }
    }
}
