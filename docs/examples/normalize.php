// @s
// @s
<?php

use ShipEngine\Model\Address;

use ShipEngine\ShipEngine;

$shipengine = new ShipEngine(['api_key' => 'MYAPIKEY']);


// @s
$normalized = $shipengine->normalizeAddress(
  ['1060 W Addison St'],
  'Chicago',
  'IL',
  '60613',
  'US'
);

echo $normalized->postal_code; // outputs: "60613-4566"


// @s
try {
  $normalized = $shipengine->normalizeAddress(['501 Crawford St'], 'Houston', 'TX');
} catch (Exception $e) {
  echo get_class($e); // outputs: "ShipEngine\Message\Error"
}


// @s
$query = Address\Query(
  ['401 E Jefferson St'],
  'Phoenix',
  'AZ',
  '85004',
  'US'
);

$normalized = $shipengine->addresses->normalize($query);

var_dump($normalized);


