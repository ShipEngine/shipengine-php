// @s
// @s
<?php

use ShipEngine\Model\Tracking;

use ShipEngine\ShipEngine;

$shipengine = new ShipEngine(['api_key' => 'MYAPIKEY']);


// @s
$information = $shipengine->trackShipment("usps", "ABC123");

echo $information->estimated_delivery;


// @s
$query = new Tracking\Query("usps", "ABC123");

$information = $shipengine->trackShipment($query);

echo $information->estimated_delivery;


// @s
$information = $shipengine->trackShipment("se-ABC123");

echo $information->estimated_delivery;


// @s
try {
  $information = $shipengine->trackShipment("se-IDONTEXIST");
} catch (Exception $e) {
  echo get_class($e); // outputs: "ShipEngine\Message\Error"
}


// @s
$result = $shipengine->tracking->query("se-ABC123");

echo $result->information->estimated_delivery;
foreach ($result->errors() as $error) {
  echo $error->getMessage();
}


