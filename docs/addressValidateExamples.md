Address Validation Documentation
================================

There are two ways to validate an address using this SDK.

- Single Address Validation - `validateAddress(Address $address, $config)`
- Normalize an Address - `normalizeAddress(Address $address, $config)`

`validateAddress(Address $address, $config)` - Validate a single address.
=========================================================================

- The `validateAddress` method takes in an array containing address information, which would typically be a set of
  **Address Line 1, Address Line 2, and Address Line 3** within a `street` array. This object should also have a
  `city_locality`, `state_province`, `postal_code`, `country_code`, `name`, `phone`, and `company_name`. This method
  requires a `country_code` which should be the 2 character capitalized abbreviation for a given country.

- **Behavior**: The `validateAddress` method will always return
  an [AddressValidateResult](../src/Model/Address/AddressValidateResult.php), even in the even that the address passed
  in was not *valid*.

- **Method level configuration** - You can optionally pass in an array that contains `configuration` values to be used
  for the current method call. The options are `api_key`, `base_url`, `page_size`,
  `retries` **(MUST be of type `DateInterval` e.g. `new DateInterval('PT5S')` would be 5 seconds)**,
  `timeout`, and `event_listener`.

> Learn more about `DateInterval()` in the php manual:
> [DateInterval PHP Manual](https://www.php.net/manual/en/class.dateinterval.php "DateInterval Documentation")

Address Array Keys/Values:
--------------------------

- **street** *array* `required`
- **city** *string*
- **state** *string*
- **postal_code** *string*
- **country_code** *string* `required`
- **residential** *boolean*
- **name** *string*
- **phone** *string*
- **company** *string*

Examples:
=========

**Successful Address Validation:** - This example illustrates the concecpts covered above:
  - Instantiate the ShipEngine class.
  - Create an Address object.
  - Use the `validateAddress` method to validate the address by passing in the Address object you created in the previous step.
  - Print out the result to view the normalized address.

```php
<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use ShipEngine\Model\Address\Address;
use ShipEngine\ShipEngine;

$api_key = getenv('SHIPENGINE_API_KEY');

$shipengine = new ShipEngine($api_key);

$address = new Address(
    [
        'street' => array('4 Jersey St', 'ste 200'),
        'city_locality' => 'Boston',
        'state_province' => 'MA',
        'postal_code' => '02215',
        'country_code' => 'US',
        'residential' => null,
        'name' => 'Bruce Wayne',
        'phone' => '123-456-7891',
        'company_name' => 'ShipEngine'
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
    [normalized_address] => ShipEngine\Model\Address\Address Object
        (
            [street:ShipEngine\Model\Address\Address:private] => Array
                (
                    [0] => 4 JERSEY ST
                )

            [city_locality:ShipEngine\Model\Address\Address:private] => BOSTON
            [state_province:ShipEngine\Model\Address\Address:private] => MA
            [postal_code:ShipEngine\Model\Address\Address:private] => 02215
            [country_code:ShipEngine\Model\Address\Address:private] => US
            [residential:ShipEngine\Model\Address\Address:private] =>
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

    [request_id] => req_H3C6E5ovPueNYeik5dnRwa
)
```

Continuing with the example at the top, you can also serialize the `Address` Type to a JSON string by using
the `jsonSerialize()` method. View the example below:

```php
... Omitted Code

$validated_address = $shipengine->validateAddress($address, ['retries' => 2]);

print_r($validated_address->jsonSerialize());  // Return the Address Type as a JSON string.
```

**Successful Address Validation Output:**: This is the `AddressValidateResult` Type serialized as JSON.
```json5
{
  "valid": true,
  "normalized_address": {
    "address": {
      "name": "BRUCE WAYNE",
      "phone": "1234567891",
      "company_name": "SHIPENGINE",
      "street": [
        "4 JERSEY ST"
      ],
      "city_locality": "BOSTON",
      "state_province": "MA",
      "postal_code": "02215",
      "country_code": "US",
      "residential": false
    }
  },
  "info": [],
  "warnings": [],
  "errors": [],
  "request_id": "req_UGF4BfDHcRc2GCwaFCKwNs"
}
```

`normalizeAddress(Address $address, $config)` - Normalize a given address.
==========================================================================

- The `normalizeAddress` method accepts the same address object as the `validateAddress` method.

**Successful Address Normalization**
```php
... Omitted Code

$normalized_address = $shipengine->normalizeAddress($address, ['retries' => 2]);

print_r($normalized_address);
```
**Successful Address Normalization Output**: As a raw `Address` object.
```php
ShipEngine\Model\Address\Address Object
(
    [street:ShipEngine\Model\Address\Address:private] => Array
        (
            [0] => 4 JERSEY ST
        )

    [city_locality:ShipEngine\Model\Address\Address:private] => BOSTON
    [state_province:ShipEngine\Model\Address\Address:private] => MA
    [postal_code:ShipEngine\Model\Address\Address:private] => 02215
    [country_code:ShipEngine\Model\Address\Address:private] => US
    [residential:ShipEngine\Model\Address\Address:private] =>
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
    "company_name": "SHIPENGINE",
    "street": [
      "4 JERSEY ST"
    ],
    "city_locality": "BOSTON",
    "state_province": "MA",
    "postal_code": "02215",
    "country_code": "US",
    "residential": false
  }
}
```

Exceptions
==========

- These methods will only throw an error ([ShipEngineError](../src/Message/ShipEngineException.php)) if there is a
  problem if a problem occurs, such as a network error or an error response from the API. In the following example this
  error responses was triggered because there was something wrong with the `Address` provided.

```bash
ShipEngine\Message\ShipEngineError : Invalid City, State, or Zip
```
