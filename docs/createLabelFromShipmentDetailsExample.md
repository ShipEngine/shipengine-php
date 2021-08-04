Create Label From Shipment Details
======================================
[ShipEngine](www.shipengine.com) allows you programmatically create shipping labels. Please see [our docs](https://www.shipengine.com/docs/labels/create-a-label/) to learn more about creating shipping labels.

Input Parameters
-------------------------------------
The `createLabelFromShipmentDetails` method accepts shipment related params as seen in the documentation above.

Output
--------------------------------
The `createLabelFromShipmentDetails` method returns the label that was created.

Example
==============================
```php
use ShipEngine\ShipEngine;
use ShipEngine\Message\ShipEngineException;

function createLabelFromShipmentDetailsDemoFunction() {
    $client = new ShipEngine('API-Key');

    $details = [
      "shipment" => [
        "service_code" => "ups_ground",
        "ship_to" => [
          "name" => "Jane Doe",
          "address_line1" => "525 S Winchester Blvd",
          "city_locality" => "San Jose",
          "state_province" => "CA",
          "postal_code" => "95128",
          "country_code" => "US",
          "address_residential_indicator" => "yes"
        ],
        "ship_from" => [
          "name" => "John Doe",
          "company_name" => "Example Corp",
          "phone" => "555-555-5555",
          "address_line1" => "4009 Marathon Blvd",
          "city_locality" => "Austin",
          "state_province" => "TX",
          "postal_code" => "78756",
          "country_code" => "US",
          "address_residential_indicator" => "no"
        ],
        "packages" => [
          [
            "weight" => [
              "value" => 20,
              "unit" => "ounce"
            ],
            "dimensions" => [
              "height" => 6,
              "width" => 12,
              "length" => 24,
              "unit" => "inch"
            ]
          ]
        ]
      ]
    ];

    try {
        print_r($client->createLabelFromShipmentDetails($details));
    } catch (ShipEngineException $e) {
        print_r($e -> getMessage());
    }
}

createLabelFromShipmentDetailsDemoFunction();

```

Example Output
-----------------------------------------------------

### Successful Create Label From Shipment Details
```php
Array
(
    [label_id] => se-75449505
    [status] => completed
    [shipment_id] => se-144329069
    [ship_date] => 2021-08-04T00:00:00Z
    [created_at] => 2021-08-04T15:40:26.7329234Z
    [shipment_cost] => Array
        (
            [currency] => usd
            [amount] => 27.98
        )

    [insurance_cost] => Array
        (
            [currency] => usd
            [amount] => 0
        )

    [tracking_number] => 1Z63R0960328699118
    [is_return_label] =>
    [rma_number] =>
    [is_international] =>
    [batch_id] =>
    [carrier_id] => se-423888
    [service_code] => ups_ground
    [package_code] => package
    [voided] =>
    [voided_at] =>
    [label_format] => pdf
    [display_scheme] => label
    [label_layout] => 4x6
    [trackable] => 1
    [label_image_id] =>
    [carrier_code] => ups
    [tracking_status] => in_transit
    [label_download] => Array
        (
            [pdf] => https://api.shipengine.com/v1/downloads/10/VF_Xyq2J002-GxtKSn_Plw/label-75449505.pdf
            [png] => https://api.shipengine.com/v1/downloads/10/VF_Xyq2J002-GxtKSn_Plw/label-75449505.png
            [zpl] => https://api.shipengine.com/v1/downloads/10/VF_Xyq2J002-GxtKSn_Plw/label-75449505.zpl
            [href] => https://api.shipengine.com/v1/downloads/10/VF_Xyq2J002-GxtKSn_Plw/label-75449505.pdf
        )

    [form_download] =>
    [insurance_claim] =>
    [packages] => Array
        (
            [0] => Array
                (
                    [package_id] => 80068779
                    [package_code] => package
                    [weight] => Array
                        (
                            [value] => 20
                            [unit] => ounce
                        )

                    [dimensions] => Array
                        (
                            [unit] => inch
                            [length] => 24
                            [width] => 12
                            [height] => 6
                        )

                    [insured_value] => Array
                        (
                            [currency] => usd
                            [amount] => 0
                        )

                    [tracking_number] => 1Z63R0960328699118
                    [label_messages] => Array
                        (
                            [reference1] =>
                            [reference2] =>
                            [reference3] =>
                        )

                    [external_package_id] =>
                    [sequence] => 1
                )

        )

    [charge_event] => carrier_default
)
```