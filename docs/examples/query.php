// @s
// @s
<?php

use ShipEngine\Model\Address\Query;
use ShipEngine\Model\Address\QueryResult;

use ShipEngine\ShipEngine;

$shipengine = new ShipEngine(['api_key' => 'MYAPIKEY']);


// @s
$result = $shipengine->addresses->query($query);

echo count($result->errors()); // outputs: 0
var_dump($result->normalized);


