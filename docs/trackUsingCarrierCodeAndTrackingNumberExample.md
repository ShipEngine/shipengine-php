Track Using Carrier Code and Tracking Number
======================================
[ShipEngine](www.shipengine.com) allows you to track a package for a given carrier and tracking number. Please see [our docs](https://www.shipengine.com/docs/tracking/) to learn more about tracking shipments.

Input Parameters
-------------------------------------

The `trackUsingCarrierCodeAndTrackingNumber` method requires the carrier code and tracking number of the shipment being tracked.

Output
--------------------------------
The `trackUsingCarrierCodeAndTrackingNumber` method returns tracking information associated with the shipment for the carrier code and tracking number.

Example
==============================
```php
use ShipEngine\ShipEngine;
use ShipEngine\Message\ShipEngineException;

function trackUsingCarrierCodeAndTrackingNumberDemoFunction() {
  $client = new ShipEngine('API-Key');
  try {
    print_r($client->trackUsingCarrierCodeAndTrackingNumber('stamps_com', '9405511899223197428490'));
  } catch (ShipEngineException $e) {
    print_r($e -> getMessage());
  }
}

trackUsingCarrierCodeAndTrackingNumberDemoFunction();
```

Example Output
-----------------------------------------------------

### Tracking Result
```php
Array
(
    [tracking_number] => 9405511899223197428490
    [tracking_url] => https://tools.usps.com/go/TrackConfirmAction.action?tLabels=9405511899223197428490
    [status_code] => NY
    [carrier_code] => stamps_com
    [carrier_id] => 1
    [carrier_detail_code] =>
    [status_description] => Not Yet In System
    [carrier_status_code] => -2147219283
    [carrier_status_description] => A status update is not yet available for this tracking number.  More information will become available when USPS receives the tracking information, or when the package is received by USPS.
    [ship_date] =>
    [estimated_delivery_date] =>
    [actual_delivery_date] =>
    [exception_description] =>
    [events] => Array
        (
        )

)
```