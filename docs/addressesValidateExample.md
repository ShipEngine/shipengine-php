Validate Addresses
================================
[ShipEngine](www.shipengine.com) allows you to validate an array of addresses before using it to create a shipment to ensure
accurate delivery of your packages. Please see [our docs](https://www.shipengine.com/docs/addresses/validation/) to learn more about validating addresses.

Input Parameters
------------------------------------
The `validateAddresses` method accepts an array of addresses as seen in the documentation above.

Output
--------------------------------
The `validateAddresses` method returns an array of address validation result objects.

Example
==============================
```php
use ShipEngine\ShipEngine;
use ShipEngine\Message\ShipEngineException;

function validateAddressesDemoFunction() {
  $client = new ShipEngine('API-Key');

  $address = [
    [
      "name" => "John Smith",
      "company_name" => "ShipStation",
      "address_line1" => "3800 N Lamar Blvd",
      "address_line2" => "#220",
      "postal_code" => '78756',
      "country_code" => "US",
      "address_residential_indicator" => 'no',
    ], [
      "name" => "John Smith",
      "company" => "ShipMate",
      "city_locality" => "Toronto",
      "state_province" => "On",
      "postal_code" => "M6K 3C3",
      "country_code" => "CA",
      "address_line1" => "123 Foo",
    ]
  ];

  try {
    print_r($client->validateAddresses($address));
  } catch (ShipEngineException $e) {
    print_r($e -> getMessage());
  }
}

validateAddressesDemoFunction();

```

Example Output
-----------------------------------------------------

### Array of connected carrier accounts
```php
Array
(
    [0] => Array
        (
            [status] => verified
            [original_address] => Array
                (
                    [name] => John Smith
                    [phone] =>
                    [company_name] => ShipStation
                    [address_line1] => 3800 N Lamar Blvd
                    [address_line2] => #220
                    [address_line3] =>
                    [city_locality] =>
                    [state_province] =>
                    [postal_code] => 78756
                    [country_code] => US
                    [address_residential_indicator] => no
                )

            [matched_address] => Array
                (
                    [name] => JOHN SMITH
                    [phone] =>
                    [company_name] => SHIPSTATION
                    [address_line1] => 3800 N LAMAR BLVD STE 220
                    [address_line2] =>
                    [address_line3] =>
                    [city_locality] => AUSTIN
                    [state_province] => TX
                    [postal_code] => 78756-0003
                    [country_code] => US
                    [address_residential_indicator] => no
                )

            [messages] => Array
                (
                )

        )

    [1] => Array
        (
            [status] => error
            [original_address] => Array
                (
                    [name] => John Smith
                    [phone] =>
                    [company_name] =>
                    [address_line1] => 123 Foo
                    [address_line2] =>
                    [address_line3] =>
                    [city_locality] => Toronto
                    [state_province] => On
                    [postal_code] => M6K 3C3
                    [country_code] => CA
                    [address_residential_indicator] => unknown
                )

            [matched_address] =>
            [messages] => Array
                (
                    [0] => Array
                        (
                            [code] => a1002
                            [message] => Could not match the inputted street name to a unique street name. No matches or too many matches were found.
                            [type] => error
                            [detail_code] => street_does_not_match_unique_street_name
                        )

                    [1] => Array
                        (
                            [code] => a1004
                            [message] => This address has been partially verified down to the city level. This is NOT the highest level possible with the data provided.
                            [type] => error
                            [detail_code] => partially_verified_to_city_level
                        )

                )

        )

)
```