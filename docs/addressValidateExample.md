Address Validation Documentation
================================
ShipEngine allows you to validate an address before using it to create a shipment to ensure accurate delivery
of your packages.

Address validation can lead to reduced shipping costs by preventing address correction surcharges. ShipEngine
cross-references multiple databases to validate addresses and identify potential delivery issues and supports address
validation for virtually every countryCode on Earth, including the United States, Canada, Great Britain, Australia,
Germany, France, Norway, Spain, Sweden, Israel, Italy, and over 160 others.

---

There are two ways to validate an address using this SDK.

- Single Address Validation - `validateAddress(Address $address, $config)`
- Normalize an Address - [normalizeAddress(Address $address, $config)](./normalizeAddressExample.md)

`validateAddress(Address $address, $config)` - Validate a single address.
=========================================================================

- The `validateAddress` method takes in an array containing address information, which would typically be a set of
  **Address Line 1, Address Line 2, and Address Line 3** within a `street` array. This object should also have a
  `cityLocality`, `stateProvince`, `postalCode`, `countryCode`, `name`, `phone`, and `company`. This method
  requires a `countryCode` which should be the 2 character capitalized abbreviation for a given countryCode.

- **Behavior**: The `validateAddress` method will always return
  an [AddressValidateResult](../src/Model/Address/AddressValidateResult.php), even in the even that the address passed
  in was not *valid*.

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

**Successful Address Validation:** - This example illustrates the following:
  - Instantiate the ShipEngine class.
  - Create an Address object.
  - Use the `validateAddress` method to validate the address by passing in the Address object you created in the previous step.
  - Print out the result to view validated address/address validation response from ShipEngine API.

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

$validated_address = $shipengine->validateAddress($address, ['retries' => 2]);

print_r($validated_address);
```

**Successful Address Validation Output:**: As a raw `AddressValidateResult` object.

```php
ShipEngine\Model\Address\AddressValidateResult Object
(
    [valid] => 1
    [normalizedAddress] => ShipEngine\Model\Address\Address Object
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

    [info] => Array
        (
        )

    [warnings] => Array
        (
        )

    [errors] => Array
        (
        )

    [requestId] => req_H3C6E5ovPueNYeik5dnRwa
)
```

Continuing with the example at the top, you can also serialize the `Address` Type to a JSON string by using
the `jsonSerialize()` method. View the example below:

```php
... Omitted Code

$validated_address = $shipengine->validateAddress($address, ['retries' => 2]);

print_r(json_encode($validated_address));  // Return the AddressValidateResult Type as a JSON string.
```

**Successful Address Validation Output:**: This is the `AddressValidateResult` Type serialized as JSON.
```json5
{
  "valid": true,
  "normalizedAddress": {
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
  },
  "info": [],
  "warnings": [],
  "errors": [],
  "requestId": "req_UGF4BfDHcRc2GCwaFCKwNs"
}
```

Exceptions
==========

- This method will only throw an exception that is an instance/extension of
  ([ShipEngineException](../src/Message/ShipEngineException.php)) if there is a problem if a problem occurs, such as a
  network error or an error response from the API.
