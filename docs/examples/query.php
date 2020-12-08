// @s
// @s
<?php

use ShipEngine\Model\AddressQuery;
use ShipEngine\Model\AddressQueryResult;

use ShipEngine\ShipEngine;

$shipengine = new ShipEngine(['api_key' => 'MYAPIKEY']);


// @s
$result = $shipengine->addresses->query($query);

echo count($result->errors()); // outputs: 0
var_dump($result->normalized);


