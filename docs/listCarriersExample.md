List Carriers
======================================
[ShipEngine](www.shipengine.com) allows you to connect
your own carrier accounts through the ShipEngine [dashboard](https://www.shipengine.com/docs/carriers/setup/). You can list all the carrier accounts you have connected with the `listCarriers` method. To learn more about carrier accounts please see [our docs](https://www.shipengine.com/docs/reference/list-carriers/).

Output
--------------------------------
The `listCarriers` method returns an array of connected carrier accounts.

Example
==============================
```php
use ShipEngine\ShipEngine;
use ShipEngine\Message\ShipEngineException;

function listCarriersDemoFunction() {
  $client = new ShipEngine('API-Key');
  try {
    print_r($client->listCarriers());
  } catch (ShipEngineException $e) {
    print_r($e -> getMessage());
  }
}

listCarriersDemoFunction();
```

Example Output
-----------------------------------------------------

### Array of connected carrier accounts
```php
Array
(
  [carriers] => Array
      (
          [0] => Array
              (
                  [carrier_id] => se-423887
                  [carrier_code] => stamps_com
                  [account_number] => test_account_423887
                  [requires_funded_amount] => 1
                  [balance] => 8949.18
                  [nickname] => ShipEngine Test Account - Stamps.com
                  [friendly_name] => Stamps.com
                  [primary] =>
                  [has_multi_package_supporting_services] =>
                  [supports_label_messages] => 1
                  [services] => Array
                      (
                          [0] => Array
                              (
                                  [carrier_id] => se-423887
                                  [carrier_code] => stamps_com
                                  [service_code] => usps_first_class_mail
                                  [name] => USPS First Class Mail
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] =>
                              )

                          [1] => Array
                              (
                                  [carrier_id] => se-423887
                                  [carrier_code] => stamps_com
                                  [service_code] => usps_media_mail
                                  [name] => USPS Media Mail
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] =>
                              )

                          [2] => Array
                              (
                                  [carrier_id] => se-423887
                                  [carrier_code] => stamps_com
                                  [service_code] => usps_parcel_select
                                  [name] => USPS Parcel Select Ground
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] =>
                              )

                          [3] => Array
                              (
                                  [carrier_id] => se-423887
                                  [carrier_code] => stamps_com
                                  [service_code] => usps_priority_mail
                                  [name] => USPS Priority Mail
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] =>
                              )

                          [4] => Array
                              (
                                  [carrier_id] => se-423887
                                  [carrier_code] => stamps_com
                                  [service_code] => usps_priority_mail_express
                                  [name] => USPS Priority Mail Express
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] =>
                              )

                          [5] => Array
                              (
                                  [carrier_id] => se-423887
                                  [carrier_code] => stamps_com
                                  [service_code] => usps_first_class_mail_international
                                  [name] => USPS First Class Mail Intl
                                  [domestic] =>
                                  [international] => 1
                                  [is_multi_package_supported] =>
                              )

                          [6] => Array
                              (
                                  [carrier_id] => se-423887
                                  [carrier_code] => stamps_com
                                  [service_code] => usps_priority_mail_international
                                  [name] => USPS Priority Mail Intl
                                  [domestic] =>
                                  [international] => 1
                                  [is_multi_package_supported] =>
                              )

                          [7] => Array
                              (
                                  [carrier_id] => se-423887
                                  [carrier_code] => stamps_com
                                  [service_code] => usps_priority_mail_express_international
                                  [name] => USPS Priority Mail Express Intl
                                  [domestic] =>
                                  [international] => 1
                                  [is_multi_package_supported] =>
                              )

                      )

                  [packages] => Array
                      (
                          [0] => Array
                              (
                                  [package_id] =>
                                  [package_code] => cubic
                                  [name] => Cubic
                                  [description] => Cubic
                              )

                          [1] => Array
                              (
                                  [package_id] =>
                                  [package_code] => flat_rate_envelope
                                  [name] => Flat Rate Envelope
                                  [description] => USPS flat rate envelope. A special cardboard envelope provided by the USPS that clearly indicates "Flat Rate".
                              )

                          [2] => Array
                              (
                                  [package_id] =>
                                  [package_code] => flat_rate_legal_envelope
                                  [name] => Flat Rate Legal Envelope
                                  [description] => Flat Rate Legal Envelope
                              )

                          [3] => Array
                              (
                                  [package_id] =>
                                  [package_code] => flat_rate_padded_envelope
                                  [name] => Flat Rate Padded Envelope
                                  [description] => Flat Rate Padded Envelope
                              )

                          [4] => Array
                              (
                                  [package_id] =>
                                  [package_code] => large_envelope_or_flat
                                  [name] => Large Envelope or Flat
                                  [description] => Large envelope or flat. Has one dimension that is between 11 1/2 and 15 long, 6 1/18 and 12 high, or 1/4 and 3/4 thick.
                              )

                          [5] => Array
                              (
                                  [package_id] =>
                                  [package_code] => large_flat_rate_box
                                  [name] => Large Flat Rate Box
                                  [description] => Large Flat Rate Box
                              )

                          [6] => Array
                              (
                                  [package_id] =>
                                  [package_code] => large_package
                                  [name] => Large Package (any side > 12)
                                  [description] => Large package. Longest side plus the distance around the thickest part is over 84 and less than or equal to 108.
                              )

                          [7] => Array
                              (
                                  [package_id] =>
                                  [package_code] => letter
                                  [name] => Letter
                                  [description] => Letter
                              )

                          [8] => Array
                              (
                                  [package_id] =>
                                  [package_code] => medium_flat_rate_box
                                  [name] => Medium Flat Rate Box
                                  [description] => USPS flat rate box. A special 11 x 8 1/2 x 5 1/2 or 14 x 3.5 x 12 USPS box that clearly indicates "Flat Rate Box"
                              )

                          [9] => Array
                              (
                                  [package_id] =>
                                  [package_code] => non_rectangular
                                  [name] => Non Rectangular Package
                                  [description] => Non-Rectangular package type that is cylindrical in shape.
                              )

                          [10] => Array
                              (
                                  [package_id] =>
                                  [package_code] => package
                                  [name] => Package
                                  [description] => Package. Longest side plus the distance around the thickest part is less than or equal to 84
                              )

                          [11] => Array
                              (
                                  [package_id] =>
                                  [package_code] => regional_rate_box_a
                                  [name] => Regional Rate Box A
                                  [description] => Regional Rate Box A
                              )

                          [12] => Array
                              (
                                  [package_id] =>
                                  [package_code] => regional_rate_box_b
                                  [name] => Regional Rate Box B
                                  [description] => Regional Rate Box B
                              )

                          [13] => Array
                              (
                                  [package_id] =>
                                  [package_code] => small_flat_rate_box
                                  [name] => Small Flat Rate Box
                                  [description] => Small Flat Rate Box
                              )

                          [14] => Array
                              (
                                  [package_id] =>
                                  [package_code] => thick_envelope
                                  [name] => Thick Envelope
                                  [description] => Thick envelope. Envelopes or flats greater than 3/4 at the thickest point.
                              )

                      )

                  [options] => Array
                      (
                          [0] => Array
                              (
                                  [name] => non_machinable
                                  [default_value] => false
                                  [description] =>
                              )

                          [1] => Array
                              (
                                  [name] => bill_to_account
                                  [default_value] =>
                                  [description] => Bill To Account
                              )

                          [2] => Array
                              (
                                  [name] => bill_to_party
                                  [default_value] =>
                                  [description] => Bill To Party
                              )

                          [3] => Array
                              (
                                  [name] => bill_to_postal_code
                                  [default_value] =>
                                  [description] => Bill To Postal Code
                              )

                          [4] => Array
                              (
                                  [name] => bill_to_country_code
                                  [default_value] =>
                                  [description] => Bill To Country Code
                              )

                      )

              )

          [1] => Array
              (
                  [carrier_id] => se-423888
                  [carrier_code] => ups
                  [account_number] => test_account_423888
                  [requires_funded_amount] =>
                  [balance] => 0
                  [nickname] => ShipEngine Test Account - UPS
                  [friendly_name] => UPS
                  [primary] =>
                  [has_multi_package_supporting_services] => 1
                  [supports_label_messages] => 1
                  [services] => Array
                      (
                          [0] => Array
                              (
                                  [carrier_id] => se-423888
                                  [carrier_code] => ups
                                  [service_code] => ups_standard_international
                                  [name] => UPS Standard®
                                  [domestic] =>
                                  [international] => 1
                                  [is_multi_package_supported] => 1
                              )

                          [1] => Array
                              (
                                  [carrier_id] => se-423888
                                  [carrier_code] => ups
                                  [service_code] => ups_next_day_air_early_am
                                  [name] => UPS Next Day Air® Early
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] => 1
                              )

                          [2] => Array
                              (
                                  [carrier_id] => se-423888
                                  [carrier_code] => ups
                                  [service_code] => ups_worldwide_express
                                  [name] => UPS Worldwide Express®
                                  [domestic] =>
                                  [international] => 1
                                  [is_multi_package_supported] => 1
                              )

                          [3] => Array
                              (
                                  [carrier_id] => se-423888
                                  [carrier_code] => ups
                                  [service_code] => ups_next_day_air
                                  [name] => UPS Next Day Air®
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] => 1
                              )

                          [4] => Array
                              (
                                  [carrier_id] => se-423888
                                  [carrier_code] => ups
                                  [service_code] => ups_ground_international
                                  [name] => UPS Ground® (International)
                                  [domestic] =>
                                  [international] => 1
                                  [is_multi_package_supported] => 1
                              )

                          [5] => Array
                              (
                                  [carrier_id] => se-423888
                                  [carrier_code] => ups
                                  [service_code] => ups_worldwide_express_plus
                                  [name] => UPS Worldwide Express Plus®
                                  [domestic] =>
                                  [international] => 1
                                  [is_multi_package_supported] => 1
                              )

                          [6] => Array
                              (
                                  [carrier_id] => se-423888
                                  [carrier_code] => ups
                                  [service_code] => ups_next_day_air_saver
                                  [name] => UPS Next Day Air Saver®
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] => 1
                              )

                          [7] => Array
                              (
                                  [carrier_id] => se-423888
                                  [carrier_code] => ups
                                  [service_code] => ups_worldwide_expedited
                                  [name] => UPS Worldwide Expedited®
                                  [domestic] =>
                                  [international] => 1
                                  [is_multi_package_supported] => 1
                              )

                          [8] => Array
                              (
                                  [carrier_id] => se-423888
                                  [carrier_code] => ups
                                  [service_code] => ups_2nd_day_air_am
                                  [name] => UPS 2nd Day Air AM®
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] => 1
                              )

                          [9] => Array
                              (
                                  [carrier_id] => se-423888
                                  [carrier_code] => ups
                                  [service_code] => ups_2nd_day_air
                                  [name] => UPS 2nd Day Air®
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] => 1
                              )

                          [10] => Array
                              (
                                  [carrier_id] => se-423888
                                  [carrier_code] => ups
                                  [service_code] => ups_worldwide_saver
                                  [name] => UPS Worldwide Saver®
                                  [domestic] =>
                                  [international] => 1
                                  [is_multi_package_supported] => 1
                              )

                          [11] => Array
                              (
                                  [carrier_id] => se-423888
                                  [carrier_code] => ups
                                  [service_code] => ups_2nd_day_air_international
                                  [name] => UPS 2nd Day Air® (International)
                                  [domestic] =>
                                  [international] => 1
                                  [is_multi_package_supported] => 1
                              )

                          [12] => Array
                              (
                                  [carrier_id] => se-423888
                                  [carrier_code] => ups
                                  [service_code] => ups_3_day_select
                                  [name] => UPS 3 Day Select®
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] => 1
                              )

                          [13] => Array
                              (
                                  [carrier_id] => se-423888
                                  [carrier_code] => ups
                                  [service_code] => ups_ground
                                  [name] => UPS® Ground
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] => 1
                              )

                          [14] => Array
                              (
                                  [carrier_id] => se-423888
                                  [carrier_code] => ups
                                  [service_code] => ups_next_day_air_international
                                  [name] => UPS Next Day Air® (International)
                                  [domestic] =>
                                  [international] => 1
                                  [is_multi_package_supported] => 1
                              )

                      )

                  [packages] => Array
                      (
                          [0] => Array
                              (
                                  [package_id] =>
                                  [package_code] => package
                                  [name] => Package
                                  [description] => Package. Longest side plus the distance around the thickest part is less than or equal to 84
                              )

                          [1] => Array
                              (
                                  [package_id] =>
                                  [package_code] => ups__express_box_large
                                  [name] => UPS  Express® Box - Large
                                  [description] => Express Box - Large
                              )

                          [2] => Array
                              (
                                  [package_id] =>
                                  [package_code] => ups_10_kg_box
                                  [name] => UPS 10 KG Box®
                                  [description] => 10 KG Box
                              )

                          [3] => Array
                              (
                                  [package_id] =>
                                  [package_code] => ups_25_kg_box
                                  [name] => UPS 25 KG Box®
                                  [description] => 25 KG Box
                              )

                          [4] => Array
                              (
                                  [package_id] =>
                                  [package_code] => ups_express_box
                                  [name] => UPS Express® Box
                                  [description] => Express Box
                              )

                          [5] => Array
                              (
                                  [package_id] =>
                                  [package_code] => ups_express_box_medium
                                  [name] => UPS Express® Box - Medium
                                  [description] => Express Box - Medium
                              )

                          [6] => Array
                              (
                                  [package_id] =>
                                  [package_code] => ups_express_box_small
                                  [name] => UPS Express® Box - Small
                                  [description] => Express Box - Small
                              )

                          [7] => Array
                              (
                                  [package_id] =>
                                  [package_code] => ups_express_pak
                                  [name] => UPS Express® Pak
                                  [description] => Pak
                              )

                          [8] => Array
                              (
                                  [package_id] =>
                                  [package_code] => ups_letter
                                  [name] => UPS Letter
                                  [description] => Letter
                              )

                          [9] => Array
                              (
                                  [package_id] =>
                                  [package_code] => ups_tube
                                  [name] => UPS Tube
                                  [description] => Tube
                              )

                      )

                  [options] => Array
                      (
                          [0] => Array
                              (
                                  [name] => bill_to_account
                                  [default_value] =>
                                  [description] =>
                              )

                          [1] => Array
                              (
                                  [name] => bill_to_country_code
                                  [default_value] =>
                                  [description] =>
                              )

                          [2] => Array
                              (
                                  [name] => bill_to_party
                                  [default_value] =>
                                  [description] =>
                              )

                          [3] => Array
                              (
                                  [name] => bill_to_postal_code
                                  [default_value] =>
                                  [description] =>
                              )

                          [4] => Array
                              (
                                  [name] => collect_on_delivery
                                  [default_value] =>
                                  [description] =>
                              )

                          [5] => Array
                              (
                                  [name] => contains_alcohol
                                  [default_value] => false
                                  [description] =>
                              )

                          [6] => Array
                              (
                                  [name] => delivered_duty_paid
                                  [default_value] => false
                                  [description] =>
                              )

                          [7] => Array
                              (
                                  [name] => dry_ice
                                  [default_value] => false
                                  [description] =>
                              )

                          [8] => Array
                              (
                                  [name] => dry_ice_weight
                                  [default_value] => 0
                                  [description] =>
                              )

                          [9] => Array
                              (
                                  [name] => freight_class
                                  [default_value] =>
                                  [description] =>
                              )

                          [10] => Array
                              (
                                  [name] => non_machinable
                                  [default_value] => false
                                  [description] =>
                              )

                          [11] => Array
                              (
                                  [name] => saturday_delivery
                                  [default_value] => false
                                  [description] =>
                              )

                          [12] => Array
                              (
                                  [name] => shipper_release
                                  [default_value] => false
                                  [description] => Driver may release package without signature
                              )

                      )

              )

          [2] => Array
              (
                  [carrier_id] => se-423889
                  [carrier_code] => fedex
                  [account_number] => test_account_423889
                  [requires_funded_amount] =>
                  [balance] => 0
                  [nickname] => ShipEngine Test Account - FedEx
                  [friendly_name] => FedEx
                  [primary] =>
                  [has_multi_package_supporting_services] => 1
                  [supports_label_messages] => 1
                  [services] => Array
                      (
                          [0] => Array
                              (
                                  [carrier_id] => se-423889
                                  [carrier_code] => fedex
                                  [service_code] => fedex_ground
                                  [name] => FedEx Ground®
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] => 1
                              )

                          [1] => Array
                              (
                                  [carrier_id] => se-423889
                                  [carrier_code] => fedex
                                  [service_code] => fedex_home_delivery
                                  [name] => FedEx Home Delivery®
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] => 1
                              )

                          [2] => Array
                              (
                                  [carrier_id] => se-423889
                                  [carrier_code] => fedex
                                  [service_code] => fedex_2day
                                  [name] => FedEx 2Day®
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] => 1
                              )

                          [3] => Array
                              (
                                  [carrier_id] => se-423889
                                  [carrier_code] => fedex
                                  [service_code] => fedex_2day_am
                                  [name] => FedEx 2Day® A.M.
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] => 1
                              )

                          [4] => Array
                              (
                                  [carrier_id] => se-423889
                                  [carrier_code] => fedex
                                  [service_code] => fedex_express_saver
                                  [name] => FedEx Express Saver®
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] => 1
                              )

                          [5] => Array
                              (
                                  [carrier_id] => se-423889
                                  [carrier_code] => fedex
                                  [service_code] => fedex_standard_overnight
                                  [name] => FedEx Standard Overnight®
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] => 1
                              )

                          [6] => Array
                              (
                                  [carrier_id] => se-423889
                                  [carrier_code] => fedex
                                  [service_code] => fedex_priority_overnight
                                  [name] => FedEx Priority Overnight®
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] => 1
                              )

                          [7] => Array
                              (
                                  [carrier_id] => se-423889
                                  [carrier_code] => fedex
                                  [service_code] => fedex_first_overnight
                                  [name] => FedEx First Overnight®
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] => 1
                              )

                          [8] => Array
                              (
                                  [carrier_id] => se-423889
                                  [carrier_code] => fedex
                                  [service_code] => fedex_1_day_freight
                                  [name] => FedEx 1Day® Freight
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] => 1
                              )

                          [9] => Array
                              (
                                  [carrier_id] => se-423889
                                  [carrier_code] => fedex
                                  [service_code] => fedex_2_day_freight
                                  [name] => FedEx 2Day® Freight
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] => 1
                              )

                          [10] => Array
                              (
                                  [carrier_id] => se-423889
                                  [carrier_code] => fedex
                                  [service_code] => fedex_3_day_freight
                                  [name] => FedEx 3Day® Freight
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] => 1
                              )

                          [11] => Array
                              (
                                  [carrier_id] => se-423889
                                  [carrier_code] => fedex
                                  [service_code] => fedex_first_overnight_freight
                                  [name] => FedEx First Overnight® Freight
                                  [domestic] => 1
                                  [international] =>
                                  [is_multi_package_supported] => 1
                              )

                          [12] => Array
                              (
                                  [carrier_id] => se-423889
                                  [carrier_code] => fedex
                                  [service_code] => fedex_ground_international
                                  [name] => FedEx International Ground®
                                  [domestic] =>
                                  [international] => 1
                                  [is_multi_package_supported] => 1
                              )

                          [13] => Array
                              (
                                  [carrier_id] => se-423889
                                  [carrier_code] => fedex
                                  [service_code] => fedex_international_economy
                                  [name] => FedEx International Economy®
                                  [domestic] =>
                                  [international] => 1
                                  [is_multi_package_supported] => 1
                              )

                          [14] => Array
                              (
                                  [carrier_id] => se-423889
                                  [carrier_code] => fedex
                                  [service_code] => fedex_international_priority
                                  [name] => FedEx International Priority®
                                  [domestic] =>
                                  [international] => 1
                                  [is_multi_package_supported] => 1
                              )

                          [15] => Array
                              (
                                  [carrier_id] => se-423889
                                  [carrier_code] => fedex
                                  [service_code] => fedex_international_first
                                  [name] => FedEx International First®
                                  [domestic] =>
                                  [international] => 1
                                  [is_multi_package_supported] => 1
                              )

                          [16] => Array
                              (
                                  [carrier_id] => se-423889
                                  [carrier_code] => fedex
                                  [service_code] => fedex_international_economy_freight
                                  [name] => FedEx International Economy® Freight
                                  [domestic] =>
                                  [international] => 1
                                  [is_multi_package_supported] => 1
                              )

                          [17] => Array
                              (
                                  [carrier_id] => se-423889
                                  [carrier_code] => fedex
                                  [service_code] => fedex_international_priority_freight
                                  [name] => FedEx International Priority® Freight
                                  [domestic] =>
                                  [international] => 1
                                  [is_multi_package_supported] => 1
                              )

                          [18] => Array
                              (
                                  [carrier_id] => se-423889
                                  [carrier_code] => fedex
                                  [service_code] => fedex_international_connect_plus
                                  [name] => FedEx International Connect Plus®
                                  [domestic] =>
                                  [international] => 1
                                  [is_multi_package_supported] =>
                              )

                      )

                  [packages] => Array
                      (
                          [0] => Array
                              (
                                  [package_id] =>
                                  [package_code] => fedex_envelope_onerate
                                  [name] => FedEx One Rate® Envelope
                                  [description] => FedEx® Envelope
                              )

                          [1] => Array
                              (
                                  [package_id] =>
                                  [package_code] => fedex_extra_large_box_onerate
                                  [name] => FedEx One Rate® Extra Large Box
                                  [description] => FedEx® Extra Large Box
                              )

                          [2] => Array
                              (
                                  [package_id] =>
                                  [package_code] => fedex_large_box_onerate
                                  [name] => FedEx One Rate® Large Box
                                  [description] => FedEx® Large Box
                              )

                          [3] => Array
                              (
                                  [package_id] =>
                                  [package_code] => fedex_medium_box_onerate
                                  [name] => FedEx One Rate® Medium Box
                                  [description] => FedEx® Medium Box
                              )

                          [4] => Array
                              (
                                  [package_id] =>
                                  [package_code] => fedex_pak_onerate
                                  [name] => FedEx One Rate® Pak
                                  [description] => FedEx® Pak
                              )

                          [5] => Array
                              (
                                  [package_id] =>
                                  [package_code] => fedex_small_box_onerate
                                  [name] => FedEx One Rate® Small Box
                                  [description] => FedEx® Small Box
                              )

                          [6] => Array
                              (
                                  [package_id] =>
                                  [package_code] => fedex_tube_onerate
                                  [name] => FedEx One Rate® Tube
                                  [description] => FedEx® Tube
                              )

                          [7] => Array
                              (
                                  [package_id] =>
                                  [package_code] => fedex_10kg_box
                                  [name] => FedEx® 10kg Box
                                  [description] => FedEx® 10kg Box
                              )

                          [8] => Array
                              (
                                  [package_id] =>
                                  [package_code] => fedex_25kg_box
                                  [name] => FedEx® 25kg Box
                                  [description] => FedEx® 25kg Box
                              )

                          [9] => Array
                              (
                                  [package_id] =>
                                  [package_code] => fedex_box
                                  [name] => FedEx® Box
                                  [description] => FedEx® Box
                              )

                          [10] => Array
                              (
                                  [package_id] =>
                                  [package_code] => fedex_envelope
                                  [name] => FedEx® Envelope
                                  [description] => FedEx® Envelope
                              )

                          [11] => Array
                              (
                                  [package_id] =>
                                  [package_code] => fedex_pak
                                  [name] => FedEx® Pak
                                  [description] => FedEx® Pak
                              )

                          [12] => Array
                              (
                                  [package_id] =>
                                  [package_code] => fedex_tube
                                  [name] => FedEx® Tube
                                  [description] => FedEx® Tube
                              )

                          [13] => Array
                              (
                                  [package_id] =>
                                  [package_code] => package
                                  [name] => Package
                                  [description] => Package. Longest side plus the distance around the thickest part is less than or equal to 84
                              )

                      )

                  [options] => Array
                      (
                          [0] => Array
                              (
                                  [name] => bill_to_account
                                  [default_value] =>
                                  [description] =>
                              )

                          [1] => Array
                              (
                                  [name] => bill_to_country_code
                                  [default_value] =>
                                  [description] =>
                              )

                          [2] => Array
                              (
                                  [name] => bill_to_party
                                  [default_value] =>
                                  [description] =>
                              )

                          [3] => Array
                              (
                                  [name] => bill_to_postal_code
                                  [default_value] =>
                                  [description] =>
                              )

                          [4] => Array
                              (
                                  [name] => collect_on_delivery
                                  [default_value] =>
                                  [description] =>
                              )

                          [5] => Array
                              (
                                  [name] => contains_alcohol
                                  [default_value] => false
                                  [description] =>
                              )

                          [6] => Array
                              (
                                  [name] => delivered_duty_paid
                                  [default_value] => false
                                  [description] =>
                              )

                          [7] => Array
                              (
                                  [name] => dry_ice
                                  [default_value] => false
                                  [description] =>
                              )

                          [8] => Array
                              (
                                  [name] => dry_ice_weight
                                  [default_value] => 0
                                  [description] =>
                              )

                          [9] => Array
                              (
                                  [name] => non_machinable
                                  [default_value] => false
                                  [description] =>
                              )

                          [10] => Array
                              (
                                  [name] => saturday_delivery
                                  [default_value] => false
                                  [description] =>
                              )

                      )

              )

      )

  [request_id] => 6768d177-5ad0-47be-bac7-e9e4d7aca3cc
  [errors] => Array
      (
      )
)
```