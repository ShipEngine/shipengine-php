Void Label With Label Id
================================
[ShipEngine](www.shipengine.com) allows you to attempt to void a previously purchased label. Please see [our docs](https://www.shipengine.com/docs/labels/voiding/) to learn more about voiding a label.

Input Parameters
-------------------------------------

The `voidLabelWithLabelId` method accepts a string that contains the label Id that is being voided.

Output
--------------------------------
The `voidLabelWithLabelId` method returns an object that indicates the status of the void label request.

Example
==============================
```php
use ShipEngine\ShipEngine;
use ShipEngine\Message\ShipEngineException;

function voidLabelWithLabelIdDemoFunction() {
  $client = new ShipEngine('API-Key');
  try {
    print_r($client->voidLabelWithLabelId('se-75449505'));
  } catch (ShipEngineException $e) {
    print_r($e -> getMessage());
  }
}

voidLabelWithLabelIdDemoFunction();
```

Example Output
-----------------------------------------------------

### Successful Void Label
```php
Array
(
    [approved] =>
    [message] => No shipment found within the allowed void period
)
```