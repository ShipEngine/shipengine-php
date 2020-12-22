// @s
// @s
<?php

use ShipEngine\Model\Address\Query;
use ShipEngine\Model\Address\QueryResult;

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


// @s
$query = new AddressQuery(
  ['1000 Elysion Park Ave'],
  'Los Angeles',
  'CA',
  'US'
);

$valid = $shipengine->validateAddress($query);


// @s
$valid = $shipengine->addresses->validate($query);


