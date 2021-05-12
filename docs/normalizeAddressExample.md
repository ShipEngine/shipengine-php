Normalize Address Documentation
===============================
ShipEngine allows you to validate an address before using it to create a shipment to ensure accurate delivery
of your packages.

Address validation can lead to reduced shipping costs by preventing address correction surcharges. ShipEngine
cross-references multiple databases to validate addresses and identify potential delivery issues and supports address
validation for virtually every countryCode on Earth, including the United States, Canada, Great Britain, Australia,
Germany, France, Norway, Spain, Sweden, Israel, Italy, and over 160 others.

---

There are two ways to validate an address using this SDK.

- Single Address Validation - [validateAddress(Address $address, $config)](./addressValidateExample.md)
- Normalize an Address - `normalizeAddress(Address $address, $config)`

`normalizeAddress(Address $address, $config)` - Normalize a given address.
==========================================================================

- The `normalizeAddress` method accepts the same address object as the `validateAddress` method as well as an `array`
containing method-level configuration options.

- **Behavior**: The `normalizeAddress` method will either return a normalized version of the address you pass in. This
  will throw an exception if address validation fails, or an invalid address is provided. The normalized address will
  be returned as an instance of the [Address](../src/Model/Address/Address.php) class.

- **Method level configuration** - You can optionally pass in an array that contains `configuration` values to be used
  for the current method call. The options are `apiKey`, `baseUrl`, `pageSize`,
  `retries` **(MUST be of type `DateInterval` e.g. `new DateInterval('PT5S')` would be 5 seconds)**,
  `timeout`, and `eventListener`.

> Learn more about `DateInterval()` in the php manual:
> [DateInterval PHP Manual](https://www.php.net/manual/en/class.dateinterval.php "DateInterval Documentation")

Address Array Keys/Values:
--------------------------

- **street** *array* `required`
- **city** *string*
- **state** *string*
- **postalCode** *string*
- **countryCode** *string* `required`
- **isResidential** *boolean*
- **name** *string*
- **phone** *string*
- **company** *string*

Examples:
=========

**Successful Address Normalization** - This example illustrates the following:
- Instantiate the ShipEngine class.
- Create an Address object.
- Use the `normalizeAddress` method to normalize a given `Address` by passing in the `Address` object you created in
  the previous step.
- Print out the result to view the normalized address object.

```php
<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use ShipEngine\Model\Address\Address;
use ShipEngine\ShipEngine;

$apiKey = getenv('SHIPENGINE_apiKey');

$shipengine = new ShipEngine($apiKey);

$address = new Address(
    [
        'street' => array('4 Jersey St', 'ste 200'),
        'cityLocality' => 'Boston',
        'stateProvince' => 'MA',
        'postalCode' => '02215',
        'countryCode' => 'US',
        'isResidential' => null,
        'name' => 'Bruce Wayne',
        'phone' => '123-456-7891',
        'company' => 'ShipEngine'
    ]
);

$normalizedAddress = $shipengine->normalizeAddress($address, ['retries' => 2]);

print_r($normalizedAddress);
```
**Successful Address Normalization Output**: As a raw `Address` object.
```php
ShipEngine\Model\Address\Address Object
(
    [street:ShipEngine\Model\Address\Address:private] => Array
        (
            [0] => 4 JERSEY ST
        )

    [cityLocality:ShipEngine\Model\Address\Address:private] => BOSTON
    [stateProvince:ShipEngine\Model\Address\Address:private] => MA
    [postalCode:ShipEngine\Model\Address\Address:private] => 02215
    [countryCode:ShipEngine\Model\Address\Address:private] => US
    [isResidential:ShipEngine\Model\Address\Address:private] =>
    [name:ShipEngine\Model\Address\Address:private] => BRUCE WAYNE
    [phone:ShipEngine\Model\Address\Address:private] => 1234567891
    [company:ShipEngine\Model\Address\Address:private] => SHIPENGINE
)
```

**Successful Address Normalization Output**: This is the `Address` Type serialized as JSON.
```json5
{
  "address": {
    "name": "BRUCE WAYNE",
    "phone": "1234567891",
    "company": "SHIPENGINE",
    "street": [
      "4 JERSEY ST"
    ],
    "cityLocality": "BOSTON",
    "stateProvince": "MA",
    "postalCode": "02215",
    "countryCode": "US",
    "isResidential": false
  }
}
```

Exceptions
==========

- This method will throw an exception that is an instance/extension of
  ([ShipEngineException](../src/Message/ShipEngineException.php)) if there is a problem with the `Address` provided, or
  if address validation fails.
