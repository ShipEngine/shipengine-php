Create Label From Rate
======================================
When retrieving rates for shipments using the `getRatesWithShipmentDetails` method, the returned information contains a `rateId` property that can be used to purchase a label without having to refill in the shipment information repeatedly. Please see [our docs](https://www.shipengine.com/docs/labels/create-from-rate/) to learn more about creating shipping labels from rates.

Input Parameters
-------------------------------------

The `createLabelFromRate` method requires a `rateId` and label params as seen in the documentation above.

Output
--------------------------------
The `createLabelFromRate` method returns the label that was created.

Example
==============================
```php
use ShipEngine\ShipEngine;
use ShipEngine\Message\ShipEngineException;

function createLabelFromRateDemoFunction() {
    $client = new ShipEngine('API-Key');

    $params = [
		  "validate_address" => "no_validation",
		  "label_layout" => "4x6",
		  "label_format" => "pdf",
		  "label_download_type" => "url",
		  "display_scheme" => "label"
		];

    try {
      print_r($client-> createLabelFromRate('se-797094871', $params));
    } catch (ShipEngineException $e) {
      print_r($e -> getMessage());
    }
}

createLabelFromRateDemoFunction();

```

Example Output
-----------------------------------------------------

### Successful Create Label From Rate Result
```php
Array
(
    [label_id] => se-75484277
    [status] => completed
    [shipment_id] => se-144316600
    [ship_date] => 2021-08-04T00:00:00Z
    [created_at] => 2021-08-04T17:29:10.3686928Z
    [shipment_cost] => Array
        (
            [currency] => usd
            [amount] => 11.63
        )

    [insurance_cost] => Array
        (
            [currency] => usd
            [amount] => 0
        )

    [tracking_number] => 9405511899560334465315
    [is_return_label] =>
    [rma_number] =>
    [is_international] =>
    [batch_id] =>
    [carrier_id] => se-423887
    [service_code] => usps_priority_mail
    [package_code] => regional_rate_box_a
    [voided] =>
    [voided_at] =>
    [label_format] => pdf
    [display_scheme] => label
    [label_layout] => 4x6
    [trackable] => 1
    [label_image_id] =>
    [carrier_code] => stamps_com
    [tracking_status] => in_transit
    [label_download] => Array
        (
            [pdf] => https://api.shipengine.com/v1/downloads/10/I_F8RgnVBEGvt7ycgHHIGg/label-75484277.pdf
            [png] => https://api.shipengine.com/v1/downloads/10/I_F8RgnVBEGvt7ycgHHIGg/label-75484277.png
            [zpl] => https://api.shipengine.com/v1/downloads/10/I_F8RgnVBEGvt7ycgHHIGg/label-75484277.zpl
            [href] => https://api.shipengine.com/v1/downloads/10/I_F8RgnVBEGvt7ycgHHIGg/label-75484277.pdf
        )

    [form_download] =>
    [insurance_claim] =>
    [packages] => Array
        (
            [0] => Array
                (
                    [package_id] => 80105682
                    [package_code] => regional_rate_box_a
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

                    [tracking_number] => 9405511899560334465315
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