Get Rates With Shipment Details
======================================
Given some shipment details and rate options, this method returns a list of rate quotes. Please see [our docs](https://www.shipengine.com/docs/rates/) to learn more about calculating rates.

Input Parameters
-------------------------------------
The `getRatesWithShipmentDetails` method accepts shipment related params as seen in the documentation above.

Output
--------------------------------
The `getRatesWithShipmentDetails` method returns the rates that were calculated for the given shipment params.

Example
==============================
```php
use ShipEngine\ShipEngine;
use ShipEngine\Message\ShipEngineException;

function getRatesWithShipmentDetailsDemoFunction() {
    $client = new ShipEngine('API-Key');

    $details = [
      "rate_options" => [
        "carrier_ids" => [
          "se-423887"
        ]
      ],
      "shipment" => [
        "validate_address" => "no_validation",
        "ship_to" => [
          "name" => "Amanda Miller",
          "phone" => "555-555-5555",
          "address_line1" => "525 S Winchester Blvd",
          "city_locality" => "San Jose",
          "state_province" => "CA",
          "postal_code" => "95128",
          "country_code" => "US",
          "address_residential_indicator" => "yes"
        ],
        "ship_from" => [
          "company_name" => "Example Corp.",
          "name" => "John Doe",
          "phone" => "111-111-1111",
          "address_line1" => "4008 Marathon Blvd",
          "address_line2" => "Suite 300",
          "city_locality" => "Austin",
          "state_province" => "TX",
          "postal_code" => "78756",
          "country_code" => "US",
          "address_residential_indicator" => "no"
        ],
        "packages" => [
          [
            "weight" => [
              "value" => 1.0,
              "unit" => "ounce"
            ]
          ]
        ]
      ]
    ];

    try {
        print_r($client->getRatesWithShipmentDetails($details));
    } catch (ShipEngineException $e) {
        print_r($e -> getMessage());
    }
}

getRatesWithShipmentDetailsDemoFunction();

```

### Array of Shipment Rates
```php
Array
(
    [rate_response] => Array
        (
            [rates] => Array
                (
                    [0] => Array
                        (
                            [rate_id] => se-797094862
                            [rate_type] => shipment
                            [carrier_id] => se-423887
                            [shipping_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0.51
                                )

                            [insurance_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [confirmation_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [other_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [zone] => 7
                            [package_type] => letter
                            [delivery_days] => 3
                            [guaranteed_service] =>
                            [estimated_delivery_date] => 2021-08-07T00:00:00Z
                            [carrier_delivery_days] => 3
                            [ship_date] => 2021-08-04T00:00:00Z
                            [negotiated_rate] =>
                            [service_type] => USPS First Class Mail
                            [service_code] => usps_first_class_mail
                            [trackable] =>
                            [carrier_code] => stamps_com
                            [carrier_nickname] => ShipEngine Test Account - Stamps.com
                            [carrier_friendly_name] => Stamps.com
                            [validation_status] => valid
                            [warning_messages] => Array
                                (
                                )

                            [error_messages] => Array
                                (
                                )

                        )

                    [1] => Array
                        (
                            [rate_id] => se-797094863
                            [rate_type] => shipment
                            [carrier_id] => se-423887
                            [shipping_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 1
                                )

                            [insurance_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [confirmation_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [other_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [zone] => 7
                            [package_type] => large_envelope_or_flat
                            [delivery_days] => 3
                            [guaranteed_service] =>
                            [estimated_delivery_date] => 2021-08-07T00:00:00Z
                            [carrier_delivery_days] => 3
                            [ship_date] => 2021-08-04T00:00:00Z
                            [negotiated_rate] =>
                            [service_type] => USPS First Class Mail
                            [service_code] => usps_first_class_mail
                            [trackable] =>
                            [carrier_code] => stamps_com
                            [carrier_nickname] => ShipEngine Test Account - Stamps.com
                            [carrier_friendly_name] => Stamps.com
                            [validation_status] => valid
                            [warning_messages] => Array
                                (
                                )

                            [error_messages] => Array
                                (
                                )

                        )

                    [2] => Array
                        (
                            [rate_id] => se-797094864
                            [rate_type] => shipment
                            [carrier_id] => se-423887
                            [shipping_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 3.35
                                )

                            [insurance_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [confirmation_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [other_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [zone] => 7
                            [package_type] => package
                            [delivery_days] => 3
                            [guaranteed_service] =>
                            [estimated_delivery_date] => 2021-08-07T00:00:00Z
                            [carrier_delivery_days] => 3
                            [ship_date] => 2021-08-04T00:00:00Z
                            [negotiated_rate] =>
                            [service_type] => USPS First Class Mail
                            [service_code] => usps_first_class_mail
                            [trackable] => 1
                            [carrier_code] => stamps_com
                            [carrier_nickname] => ShipEngine Test Account - Stamps.com
                            [carrier_friendly_name] => Stamps.com
                            [validation_status] => valid
                            [warning_messages] => Array
                                (
                                )

                            [error_messages] => Array
                                (
                                )

                        )

                    [3] => Array
                        (
                            [rate_id] => se-797094865
                            [rate_type] => shipment
                            [carrier_id] => se-423887
                            [shipping_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 8.52
                                )

                            [insurance_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [confirmation_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [other_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [zone] => 7
                            [package_type] => package
                            [delivery_days] => 2
                            [guaranteed_service] =>
                            [estimated_delivery_date] => 2021-08-06T00:00:00Z
                            [carrier_delivery_days] => 2
                            [ship_date] => 2021-08-04T00:00:00Z
                            [negotiated_rate] =>
                            [service_type] => USPS Priority Mail
                            [service_code] => usps_priority_mail
                            [trackable] => 1
                            [carrier_code] => stamps_com
                            [carrier_nickname] => ShipEngine Test Account - Stamps.com
                            [carrier_friendly_name] => Stamps.com
                            [validation_status] => valid
                            [warning_messages] => Array
                                (
                                )

                            [error_messages] => Array
                                (
                                )

                        )

                    [4] => Array
                        (
                            [rate_id] => se-797094866
                            [rate_type] => shipment
                            [carrier_id] => se-423887
                            [shipping_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 13.75
                                )

                            [insurance_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [confirmation_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [other_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [zone] => 7
                            [package_type] => medium_flat_rate_box
                            [delivery_days] => 2
                            [guaranteed_service] =>
                            [estimated_delivery_date] => 2021-08-06T00:00:00Z
                            [carrier_delivery_days] => 2
                            [ship_date] => 2021-08-04T00:00:00Z
                            [negotiated_rate] =>
                            [service_type] => USPS Priority Mail
                            [service_code] => usps_priority_mail
                            [trackable] => 1
                            [carrier_code] => stamps_com
                            [carrier_nickname] => ShipEngine Test Account - Stamps.com
                            [carrier_friendly_name] => Stamps.com
                            [validation_status] => valid
                            [warning_messages] => Array
                                (
                                )

                            [error_messages] => Array
                                (
                                )

                        )

                    [5] => Array
                        (
                            [rate_id] => se-797094867
                            [rate_type] => shipment
                            [carrier_id] => se-423887
                            [shipping_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 7.9
                                )

                            [insurance_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [confirmation_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [other_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [zone] => 7
                            [package_type] => small_flat_rate_box
                            [delivery_days] => 2
                            [guaranteed_service] =>
                            [estimated_delivery_date] => 2021-08-06T00:00:00Z
                            [carrier_delivery_days] => 2
                            [ship_date] => 2021-08-04T00:00:00Z
                            [negotiated_rate] =>
                            [service_type] => USPS Priority Mail
                            [service_code] => usps_priority_mail
                            [trackable] => 1
                            [carrier_code] => stamps_com
                            [carrier_nickname] => ShipEngine Test Account - Stamps.com
                            [carrier_friendly_name] => Stamps.com
                            [validation_status] => valid
                            [warning_messages] => Array
                                (
                                )

                            [error_messages] => Array
                                (
                                )

                        )

                    [6] => Array
                        (
                            [rate_id] => se-797094868
                            [rate_type] => shipment
                            [carrier_id] => se-423887
                            [shipping_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 19.3
                                )

                            [insurance_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [confirmation_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [other_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [zone] => 7
                            [package_type] => large_flat_rate_box
                            [delivery_days] => 2
                            [guaranteed_service] =>
                            [estimated_delivery_date] => 2021-08-06T00:00:00Z
                            [carrier_delivery_days] => 2
                            [ship_date] => 2021-08-04T00:00:00Z
                            [negotiated_rate] =>
                            [service_type] => USPS Priority Mail
                            [service_code] => usps_priority_mail
                            [trackable] => 1
                            [carrier_code] => stamps_com
                            [carrier_nickname] => ShipEngine Test Account - Stamps.com
                            [carrier_friendly_name] => Stamps.com
                            [validation_status] => valid
                            [warning_messages] => Array
                                (
                                )

                            [error_messages] => Array
                                (
                                )

                        )

                    [7] => Array
                        (
                            [rate_id] => se-797094869
                            [rate_type] => shipment
                            [carrier_id] => se-423887
                            [shipping_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 7.4
                                )

                            [insurance_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [confirmation_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [other_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [zone] => 7
                            [package_type] => flat_rate_envelope
                            [delivery_days] => 2
                            [guaranteed_service] =>
                            [estimated_delivery_date] => 2021-08-06T00:00:00Z
                            [carrier_delivery_days] => 2
                            [ship_date] => 2021-08-04T00:00:00Z
                            [negotiated_rate] =>
                            [service_type] => USPS Priority Mail
                            [service_code] => usps_priority_mail
                            [trackable] => 1
                            [carrier_code] => stamps_com
                            [carrier_nickname] => ShipEngine Test Account - Stamps.com
                            [carrier_friendly_name] => Stamps.com
                            [validation_status] => valid
                            [warning_messages] => Array
                                (
                                )

                            [error_messages] => Array
                                (
                                )

                        )

                    [8] => Array
                        (
                            [rate_id] => se-797094870
                            [rate_type] => shipment
                            [carrier_id] => se-423887
                            [shipping_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 8
                                )

                            [insurance_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [confirmation_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [other_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [zone] => 7
                            [package_type] => flat_rate_padded_envelope
                            [delivery_days] => 2
                            [guaranteed_service] =>
                            [estimated_delivery_date] => 2021-08-06T00:00:00Z
                            [carrier_delivery_days] => 2
                            [ship_date] => 2021-08-04T00:00:00Z
                            [negotiated_rate] =>
                            [service_type] => USPS Priority Mail
                            [service_code] => usps_priority_mail
                            [trackable] => 1
                            [carrier_code] => stamps_com
                            [carrier_nickname] => ShipEngine Test Account - Stamps.com
                            [carrier_friendly_name] => Stamps.com
                            [validation_status] => valid
                            [warning_messages] => Array
                                (
                                )

                            [error_messages] => Array
                                (
                                )

                        )

                    [9] => Array
                        (
                            [rate_id] => se-797094871
                            [rate_type] => shipment
                            [carrier_id] => se-423887
                            [shipping_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 11.63
                                )

                            [insurance_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [confirmation_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [other_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [zone] => 7
                            [package_type] => regional_rate_box_a
                            [delivery_days] => 2
                            [guaranteed_service] =>
                            [estimated_delivery_date] => 2021-08-06T00:00:00Z
                            [carrier_delivery_days] => 2
                            [ship_date] => 2021-08-04T00:00:00Z
                            [negotiated_rate] =>
                            [service_type] => USPS Priority Mail
                            [service_code] => usps_priority_mail
                            [trackable] => 1
                            [carrier_code] => stamps_com
                            [carrier_nickname] => ShipEngine Test Account - Stamps.com
                            [carrier_friendly_name] => Stamps.com
                            [validation_status] => valid
                            [warning_messages] => Array
                                (
                                )

                            [error_messages] => Array
                                (
                                )

                        )

                    [10] => Array
                        (
                            [rate_id] => se-797094872
                            [rate_type] => shipment
                            [carrier_id] => se-423887
                            [shipping_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 20.1
                                )

                            [insurance_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [confirmation_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [other_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [zone] => 7
                            [package_type] => regional_rate_box_b
                            [delivery_days] => 2
                            [guaranteed_service] =>
                            [estimated_delivery_date] => 2021-08-06T00:00:00Z
                            [carrier_delivery_days] => 2
                            [ship_date] => 2021-08-04T00:00:00Z
                            [negotiated_rate] =>
                            [service_type] => USPS Priority Mail
                            [service_code] => usps_priority_mail
                            [trackable] => 1
                            [carrier_code] => stamps_com
                            [carrier_nickname] => ShipEngine Test Account - Stamps.com
                            [carrier_friendly_name] => Stamps.com
                            [validation_status] => valid
                            [warning_messages] => Array
                                (
                                )

                            [error_messages] => Array
                                (
                                )

                        )

                    [11] => Array
                        (
                            [rate_id] => se-797094873
                            [rate_type] => shipment
                            [carrier_id] => se-423887
                            [shipping_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 7.7
                                )

                            [insurance_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [confirmation_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [other_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [zone] => 7
                            [package_type] => flat_rate_legal_envelope
                            [delivery_days] => 2
                            [guaranteed_service] =>
                            [estimated_delivery_date] => 2021-08-06T00:00:00Z
                            [carrier_delivery_days] => 2
                            [ship_date] => 2021-08-04T00:00:00Z
                            [negotiated_rate] =>
                            [service_type] => USPS Priority Mail
                            [service_code] => usps_priority_mail
                            [trackable] => 1
                            [carrier_code] => stamps_com
                            [carrier_nickname] => ShipEngine Test Account - Stamps.com
                            [carrier_friendly_name] => Stamps.com
                            [validation_status] => valid
                            [warning_messages] => Array
                                (
                                )

                            [error_messages] => Array
                                (
                                )

                        )

                    [12] => Array
                        (
                            [rate_id] => se-797094874
                            [rate_type] => shipment
                            [carrier_id] => se-423887
                            [shipping_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 31.4
                                )

                            [insurance_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [confirmation_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [other_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [zone] => 7
                            [package_type] => package
                            [delivery_days] => 2
                            [guaranteed_service] =>
                            [estimated_delivery_date] => 2021-08-06T00:00:00Z
                            [carrier_delivery_days] => 1-2
                            [ship_date] => 2021-08-04T00:00:00Z
                            [negotiated_rate] =>
                            [service_type] => USPS Priority Mail Express
                            [service_code] => usps_priority_mail_express
                            [trackable] => 1
                            [carrier_code] => stamps_com
                            [carrier_nickname] => ShipEngine Test Account - Stamps.com
                            [carrier_friendly_name] => Stamps.com
                            [validation_status] => valid
                            [warning_messages] => Array
                                (
                                )

                            [error_messages] => Array
                                (
                                )

                        )

                    [13] => Array
                        (
                            [rate_id] => se-797094875
                            [rate_type] => shipment
                            [carrier_id] => se-423887
                            [shipping_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 22.75
                                )

                            [insurance_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [confirmation_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [other_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [zone] => 7
                            [package_type] => flat_rate_envelope
                            [delivery_days] => 2
                            [guaranteed_service] =>
                            [estimated_delivery_date] => 2021-08-06T00:00:00Z
                            [carrier_delivery_days] => 1-2
                            [ship_date] => 2021-08-04T00:00:00Z
                            [negotiated_rate] =>
                            [service_type] => USPS Priority Mail Express
                            [service_code] => usps_priority_mail_express
                            [trackable] => 1
                            [carrier_code] => stamps_com
                            [carrier_nickname] => ShipEngine Test Account - Stamps.com
                            [carrier_friendly_name] => Stamps.com
                            [validation_status] => valid
                            [warning_messages] => Array
                                (
                                )

                            [error_messages] => Array
                                (
                                )

                        )

                    [14] => Array
                        (
                            [rate_id] => se-797094876
                            [rate_type] => shipment
                            [carrier_id] => se-423887
                            [shipping_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 23.25
                                )

                            [insurance_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [confirmation_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [other_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [zone] => 7
                            [package_type] => flat_rate_padded_envelope
                            [delivery_days] => 2
                            [guaranteed_service] =>
                            [estimated_delivery_date] => 2021-08-06T00:00:00Z
                            [carrier_delivery_days] => 1-2
                            [ship_date] => 2021-08-04T00:00:00Z
                            [negotiated_rate] =>
                            [service_type] => USPS Priority Mail Express
                            [service_code] => usps_priority_mail_express
                            [trackable] => 1
                            [carrier_code] => stamps_com
                            [carrier_nickname] => ShipEngine Test Account - Stamps.com
                            [carrier_friendly_name] => Stamps.com
                            [validation_status] => valid
                            [warning_messages] => Array
                                (
                                )

                            [error_messages] => Array
                                (
                                )

                        )

                    [15] => Array
                        (
                            [rate_id] => se-797094877
                            [rate_type] => shipment
                            [carrier_id] => se-423887
                            [shipping_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 22.95
                                )

                            [insurance_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [confirmation_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [other_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [zone] => 7
                            [package_type] => flat_rate_legal_envelope
                            [delivery_days] => 2
                            [guaranteed_service] =>
                            [estimated_delivery_date] => 2021-08-06T00:00:00Z
                            [carrier_delivery_days] => 1-2
                            [ship_date] => 2021-08-04T00:00:00Z
                            [negotiated_rate] =>
                            [service_type] => USPS Priority Mail Express
                            [service_code] => usps_priority_mail_express
                            [trackable] => 1
                            [carrier_code] => stamps_com
                            [carrier_nickname] => ShipEngine Test Account - Stamps.com
                            [carrier_friendly_name] => Stamps.com
                            [validation_status] => valid
                            [warning_messages] => Array
                                (
                                )

                            [error_messages] => Array
                                (
                                )

                        )

                    [16] => Array
                        (
                            [rate_id] => se-797094878
                            [rate_type] => shipment
                            [carrier_id] => se-423887
                            [shipping_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 2.89
                                )

                            [insurance_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [confirmation_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [other_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [zone] => 7
                            [package_type] => package
                            [delivery_days] => 6
                            [guaranteed_service] =>
                            [estimated_delivery_date] => 2021-08-11T00:00:00Z
                            [carrier_delivery_days] => 6
                            [ship_date] => 2021-08-04T00:00:00Z
                            [negotiated_rate] =>
                            [service_type] => USPS Media Mail
                            [service_code] => usps_media_mail
                            [trackable] => 1
                            [carrier_code] => stamps_com
                            [carrier_nickname] => ShipEngine Test Account - Stamps.com
                            [carrier_friendly_name] => Stamps.com
                            [validation_status] => valid
                            [warning_messages] => Array
                                (
                                )

                            [error_messages] => Array
                                (
                                )

                        )

                    [17] => Array
                        (
                            [rate_id] => se-797094879
                            [rate_type] => shipment
                            [carrier_id] => se-423887
                            [shipping_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 7.97
                                )

                            [insurance_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [confirmation_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [other_amount] => Array
                                (
                                    [currency] => usd
                                    [amount] => 0
                                )

                            [zone] => 7
                            [package_type] => package
                            [delivery_days] => 6
                            [guaranteed_service] =>
                            [estimated_delivery_date] => 2021-08-11T00:00:00Z
                            [carrier_delivery_days] => 6
                            [ship_date] => 2021-08-04T00:00:00Z
                            [negotiated_rate] =>
                            [service_type] => USPS Parcel Select Ground
                            [service_code] => usps_parcel_select
                            [trackable] => 1
                            [carrier_code] => stamps_com
                            [carrier_nickname] => ShipEngine Test Account - Stamps.com
                            [carrier_friendly_name] => Stamps.com
                            [validation_status] => valid
                            [warning_messages] => Array
                                (
                                )

                            [error_messages] => Array
                                (
                                )

                        )

                )

            [invalid_rates] => Array
                (
                )

            [rate_request_id] => se-86895712
            [shipment_id] => se-144316600
            [created_at] => 2021-08-04T15:18:27.0623372Z
            [status] => completed
            [errors] => Array
                (
                )

        )

    [shipment_id] => se-144316600
    [carrier_id] => se-423887
    [service_code] =>
    [external_shipment_id] =>
    [ship_date] => 2021-08-04T00:00:00Z
    [created_at] => 2021-08-04T15:18:26.297Z
    [modified_at] => 2021-08-04T15:18:26.283Z
    [shipment_status] => pending
    [ship_to] => Array
        (
            [name] => Amanda Miller
            [phone] => 555-555-5555
            [company_name] =>
            [address_line1] => 525 S Winchester Blvd
            [address_line2] =>
            [address_line3] =>
            [city_locality] => San Jose
            [state_province] => CA
            [postal_code] => 95128
            [country_code] => US
            [address_residential_indicator] => yes
        )

    [ship_from] => Array
        (
            [name] => John Doe
            [phone] => 111-111-1111
            [company_name] => Example Corp.
            [address_line1] => 4008 Marathon Blvd
            [address_line2] => Suite 300
            [address_line3] =>
            [city_locality] => Austin
            [state_province] => TX
            [postal_code] => 78756
            [country_code] => US
            [address_residential_indicator] => unknown
        )

    [warehouse_id] =>
    [return_to] => Array
        (
            [name] => John Doe
            [phone] => 111-111-1111
            [company_name] => Example Corp.
            [address_line1] => 4008 Marathon Blvd
            [address_line2] => Suite 300
            [address_line3] =>
            [city_locality] => Austin
            [state_province] => TX
            [postal_code] => 78756
            [country_code] => US
            [address_residential_indicator] => unknown
        )

    [confirmation] => none
    [customs] => Array
        (
            [contents] => merchandise
            [customs_items] => Array
                (
                )

            [non_delivery] => return_to_sender
        )

    [external_order_id] =>
    [order_source_code] =>
    [advanced_options] => Array
        (
            [bill_to_account] =>
            [bill_to_country_code] =>
            [bill_to_party] =>
            [bill_to_postal_code] =>
            [contains_alcohol] =>
            [delivered_duty_paid] =>
            [non_machinable] =>
            [saturday_delivery] =>
            [dry_ice] =>
            [dry_ice_weight] =>
            [freight_class] =>
            [custom_field1] =>
            [custom_field2] =>
            [custom_field3] =>
            [collect_on_delivery] =>
        )

    [insurance_provider] => none
    [tags] => Array
        (
        )

    [packages] => Array
        (
            [0] => Array
                (
                    [package_code] => package
                    [weight] => Array
                        (
                            [value] => 1
                            [unit] => ounce
                        )

                    [dimensions] => Array
                        (
                            [unit] => inch
                            [length] => 0
                            [width] => 0
                            [height] => 0
                        )

                    [insured_value] => Array
                        (
                            [currency] => usd
                            [amount] => 0
                        )

                    [label_messages] => Array
                        (
                            [reference1] =>
                            [reference2] =>
                            [reference3] =>
                        )

                    [external_package_id] =>
                )

        )

    [total_weight] => Array
        (
            [value] => 1
            [unit] => ounce
        )

    [items] => Array
        (
        )

)
```