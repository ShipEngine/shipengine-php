<?php

require '../../vendor/autoload.php';

use ShipEngine\Model\AddressQuery;
use ShipEngine\Model\AddressQueryResult;

use ShipEngine\ShipEngine;

$shipengine = new ShipEngine(['api_key' => 'MYAPIKEY']);


// @s
$valid = $shipengine->validateAddress(
    ['1 E 161 St'],
    'The Bronx',
    'NY',
    '10451',
    'US'
);

assert($valid);


// @s
$query = new AddressQuery(
    ['1000 Elysion Park Ave'],
    'Los Angeles',
    'CA',
    'US'
);

$valid = $shipengine->validateAddress($query);

assert($valid);


// @s
$valid = $shipengine->addresses->validate($query);

assert($valid);
