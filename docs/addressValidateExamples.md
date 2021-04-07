Address Validation Documentation
================================

There are two ways to validate an address using this SDK.
- Single Address Validation
- Multiple Address Validation

`validateAddress()` -  Validate a single address.
=================================================

- The `validateAddress` method takes in an array containing street information, which would typically be
a set of **Address Line 1, Address Line 2, and Address Line 3**. You can add these as values to the `street` array.
This method also requires a `country_code` which should be the 2 character capitalized abbreviation for a given country.

- **Behavior**: The `validateAddress` method will always return an [Address Type](../src/Model/Address/AddressResult.php), even in
the even that the address passed in was not *valid*.

Method Arguments:
-----------------
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

**Successful Address Validation:**

```php
<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use ShipEngine\ShipEngine;

$api_key = getenv('SHIPENGINE_API_KEY');

$shipengine = new ShipEngine($api_key);

$validated_address = $shipengine->validateAddress(
    ['4 Jersey St', 'ste 200'],null,
    'MA',
);

print_r($validated_address);
```

**Successful Address Validation Output:** As a raw `Address` object.
```php
ShipEngine\Model\Address\Address Object
(
    [valid:ShipEngine\Model\Address\Address:private] => 1
    [address:ShipEngine\Model\Address\Address:private] => Array
        (
            [name] => ShipEngine
            [phone] => 1234567891
            [company] => ShipEngine
            [street] => Array
                (
                    [0] => 4 Jersey St
                )

            [city_locality] => Boston
            [state_province] => MA
            [postal_code] => 02215
            [country_code] => US
            [residential] =>
        )

    [messages:ShipEngine\Model\Address\Address:private] => Array
        (
            [info] => Array
                (
                )

            [errors] => Array
                (
                )

            [warnings] => Array
                (
                    [0] => There was a change or addition to the state/province.
                )

        )

)
```

Continuing with the example at the top, you can also serialize the `Address` Type to a JSON string
by using the `jsonSerialize()` method. View the example below:

```php
...

\$validated_address = \$shipengine->validateAddress(
    ['4 Jersey St', 'ste 200'],
    'Boston',
    'MA',
    '02215',
    'US',
    false,
    'ShipEngine',
    '1234567891',
    'ShipEngine',
);

print_r($validated_address->jsonSerialize());  // Return the Address Type as a JSON string.
```

**Successful Address Validation Output:** This is the `Address` Type serialized as JSON.
This example contains lorem ipsum text.
```json5
{
  "valid": true,
  "address": {
    "name": "ShipEngine",
    "phone": "1234567891",
    "company": "ShipEngine",
    "street": [
      "in nostrud consequat nisi"
    ],
    "country_code": "BK",
    "postal_code": "ullamco culpa",
    "city_locality": "aliqua",
    "residential": false
  },
  "messages": {
    "errors": [
      "aute ea nulla",
      "occaecat consequat consectetur in esse",
      "aliqua sed"
    ],
    "info": [
      "Duis",
      "voluptate sed sunt",
      "nisi irure amet",
      "dolore aute",
      "exercitation esse aliquip aute est"
    ]
  }
}
```

`validateAddresses()` - Validate multiple addresses.
====================================================
- This method takes an `array` of php objects that contain the appropriate method arguments used in the
  `validateAddress()` method. This allows you to validate multiple addresses by passing in an array of addresses.
- **Behavior:** The `validateAddresses()` method will always return an array of addresses, and will return an error
  if something goes wrong with the request itself.

Method Arguments: Multi-Address
--------------------------------
- An `array` of objects, each containing the same arguments that the `validateAddress()`
  method uses. Each address object should at minimum, provide a `street` and `country_code`.
  The complete arguments of the listed below:
  - **street** *array* `required`
  - **city** *string*
  - **state** *string*
  - **postal_code** *string*
  - **country_code** *string* `required`
  - **residential** *boolean*
  - **name** *string*
  - **phone** *string*
  - **company** *string*

```php
<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use ShipEngine\ShipEngine;

$api_key = 'SHIPENGINE_API_KEY';

$shipengine = new ShipEngine($api_key);

$validation = $shipengine->addresses->validateAddresses(
    array(
        ['4 Jersey St', 'ste 200'],
        'Boston',
        'TX',
        '02215',
        'US',
        ['4 Jersey St', 'ste 200'],
        'Boston',
        'MA',
        '02215',
        'US'
    )
);

print_r($validation);
```

**Successful Multi-Address Validation Output:** As a raw array of `Address` objects.
```php
Array
(
    [0] => ShipEngine\Model\Address\Address Object
        (
            [valid:ShipEngine\Model\Address\Address:private] => 1
            [address:ShipEngine\Model\Address\Address:private] => Array
                (
                    [name] => null
                    [phone] => null
                    [company] => null
                    [street] => Array
                        (
                            [0] => 4 Jersey St
                        )

                    [city_locality] => Boston
                    [state_province] => MA
                    [postal_code] => 02215
                    [country_code] => US
                    [residential] =>
                )

            [messages:ShipEngine\Model\Address\Address:private] => Array
                (
                    [info] => Array
                        (
                        )

                    [errors] => Array
                        (
                        )

                    [warnings] => Array
                        (
                            [0] => There was a change or addition to the state/province.
                        )

                )

        )

    [1] => ShipEngine\Model\Address\Address Object
        (
            [valid:ShipEngine\Model\Address\Address:private] => 1
            [address:ShipEngine\Model\Address\Address:private] => Array
                (
                    [name] => null
                    [phone] => null
                    [company] => null
                    [street] => Array
                        (
                            [0] => 4 Jersey St
                        )

                    [city_locality] => Boston
                    [state_province] => MA
                    [postal_code] => 02215
                    [country_code] => US
                    [residential] =>
                )

            [messages:ShipEngine\Model\Address\Address:private] => Array
                (
                    [info] => Array
                        (
                        )

                    [errors] => Array
                        (
                        )

                    [warnings] => Array
                        (
                            [0] => There was a change or addition to the state/province.
                        )

                )

        )

)
```

**Successful Multi-Address Validation Output:** This is the array of `Address` objects serialized as JSON.
```json5
[
  {
    "valid": true,
    "address": {
      "name": null,
      "phone": null,
      "company": null,
      "street": [
        "4 Jersey St"
      ],
      "city_locality": "Boston",
      "state_province": "MA",
      "postal_code": "02215",
      "country_code": "US",
      "residential": false
    },
    "messages": {
      "info": [],
      "errors": [],
      "warnings": [
        "There was a change or addition to the state/province."
      ]
    }
  },
  {
    "valid": true,
    "address": {
      "name": null,
      "phone": null,
      "company": null,
      "street": [
        "4 Jersey St"
      ],
      "city_locality": "Boston",
      "state_province": "MA",
      "postal_code": "02215",
      "country_code": "US",
      "residential": false
    },
    "messages": {
      "info": [],
      "errors": [],
      "warnings": [
        "There was a change or addition to the state/province."
      ]
    }
  }
]
```

Errors
======
- These methods will only throw an error ([ShipEngineError](../src/Message/ShipEngineException.php)) if there is a problem if a problem occurs,
  such as a network error or an error response from the API. In the following example this error responses was
  triggered because there was something wrong with the `Address` provided.

```bash
ShipEngine\Message\ShipEngineError : Invalid City, State, or Zip
```
