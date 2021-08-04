Track By Label ID
======================================
[ShipEngine](www.shipengine.com) allows you to track a package by its ShipEngine label ID. Please see [our docs](https://www.shipengine.com/docs/tracking/track-by-label-id/) to learn more about tracking shipments.

Input Parameters
-------------------------------------

The `trackUsingLabelId` method requires the ID of the label associated with the shipment you are trying to track.

Output
--------------------------------
The `trackUsingLabelId` method returns tracking information associated with the shipment for the given label ID.

Example
==============================
```php
use ShipEngine\ShipEngine;
use ShipEngine\Message\ShipEngineException;

function trackLabelWithLabelIdDemoFunction() {
  $client = new ShipEngine('API-Key');
  try {
    print_r($client->trackUsingLabelId('se-75492762'));
  } catch (ShipEngineException $e) {
    print_r($e -> getMessage());
  }
}

trackLabelWithLabelIdDemoFunction();
```

Example Output
-----------------------------------------------------

### Tracking Result
```php
Array
(
    [tracking_number] => 1Z63R0960322853130
    [tracking_url] => http://wwwapps.ups.com/WebTracking/processRequest?HTMLVersion=5.0&Requester=NES&AgreeToTermsAndConditions=yes&loc=en_US&tracknum=1Z63R0960322853130
    [status_code] => UN
    [carrier_code] => ups
    [carrier_id] => 3
    [carrier_detail_code] =>
    [status_description] => Unknown
    [carrier_status_code] =>
    [carrier_status_description] => No tracking information available
    [ship_date] =>
    [estimated_delivery_date] =>
    [actual_delivery_date] =>
    [exception_description] =>
    [events] => Array
        (
        )

)
```