Package Tracking Documentation
==============================
With ShipEngine, you can subscribe to real-time tracking events for any package – regardless of whether you
created the label via ShipEngine.

---

There are two ways to track a package using this SDK.

- Track by Carrier Code and Tracking Number - `trackPackage(TrackingQuery $trackingData, $config);`
- Track by PackageId - `trackPackage('abcFedExDelivered', $config)`

`trackPackage(string|TrackingQuery $trackingData, $config)` - Track a given package or shipment.
=========================================================================
- The `trackPackage` method can be used in two ways, you can either track by `PackageId` by passing it as a string
to this method. Alternatively, you can track by `carrierCode` and `trackingNumber` by passing in an instance
  of [TrackingQuery](../src/Model/Package/TrackingQuery.php) which has `carrierCode` and `trackingNumber` properties
  on it that are used within this method.

- **Behavior**: The `trackPackage` method will always return
  an [TrackPackageResult](../src/Model/Package/TrackPackageResult.php) object, and in the event of an exception an
  instance or extension of [ShipEngineException](../src/Message/ShipEngineException.php) will be returned.

- **Method level configuration** - You can optionally pass in an array that contains `configuration` values to be used
  for the current method call. The options are `api_key`, `baseUrl`, `pageSize`,
  `retries` **(MUST be of type `DateInterval` e.g. `new DateInterval('PT5S')` would be 5 seconds)**,
  `timeout`, and `eventListener`.

> Learn more about `DateInterval()` in the php manual:
> [DateInterval PHP Manual](https://www.php.net/manual/en/class.dateinterval.php "DateInterval Documentation")

Examples:
=========

**Successful TrackPackage by TrackingNumber and Carrier Code:** - This example illustrates the following:
- Instantiate the ShipEngine class.
- Create a `TrackingQuery` object and pass it into the `trackPackage` method.
- Use the `trackPackage` method to track a given shipment using the tracking data in the previous step.
- Print out the result to view tracking data response from ShipEngine API.

```php
<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use ShipEngine\Model\Package\TrackingQuery;
use ShipEngine\ShipEngine;

$api_key = getenv('SHIPENGINE_api_key');

$shipengine = new ShipEngine($api_key);

$trackingData = new TrackingQuery('fedex', 'abcFedExDelivered');

$trackingResult = $shipengine->trackPackage($trackingData, ['retries' => 2]);

print_r($trackingResult);
```

**Successful TrackPackage by Tracking Number and Carrier Code Output:**: As a raw `TrackPackageResult` object.
```php
ShipEngine\Model\Package\TrackPackageResult Object
(
    [shipment] => ShipEngine\Model\Package\Shipment Object
        (
            [shipmentId] =>
            [accountId] =>
            [carrierAccount] =>
            [carrier] => ShipEngine\Model\Carriers\Carrier Object
                (
                    [name] => FedEx
                    [code] => fedex
                )

            [estimatedDeliveryDate] => ShipEngine\Util\IsoString Object
                (
                    [value:ShipEngine\Util\IsoString:private] => 2021-05-18T21:00:00.000Z
                )

            [actualDeliveryDate] => ShipEngine\Util\IsoString Object
                (
                    [value:ShipEngine\Util\IsoString:private] => 2021-05-15T19:00:00.000Z
                )

        )

    [package] => ShipEngine\Model\Package\Package Object
        (
            [packageId] =>
            [weight] =>
            [dimensions] =>
            [trackingNumber] => abcFedExDelivered
            [trackingUrl] => https://www.fedex.com/track/abcFedExDelivered
        )

    [events] => Array
        (
            [0] => ShipEngine\Model\Package\TrackingEvent Object
                (
                    [dateTime] => ShipEngine\Util\IsoString Object
                        (
                            [value:ShipEngine\Util\IsoString:private] => 2021-05-13T19:00:00.000Z
                        )

                    [carrierDateTime] => ShipEngine\Util\IsoString Object
                        (
                            [value:ShipEngine\Util\IsoString:private] => 2021-05-14T01:00:00
                        )

                    [status] => accepted
                    [description] => Dropped-off at shipping center
                    [carrierStatusCode] => ACPT-2
                    [carrierDetailCode] => PU7W
                    [signer] =>
                    [location] =>
                )

            [1] => ShipEngine\Model\Package\TrackingEvent Object
                (
                    [dateTime] => ShipEngine\Util\IsoString Object
                        (
                            [value:ShipEngine\Util\IsoString:private] => 2021-05-14T01:00:00.000Z
                        )

                    [carrierDateTime] => ShipEngine\Util\IsoString Object
                        (
                            [value:ShipEngine\Util\IsoString:private] => 2021-05-14T07:00:00
                        )

                    [status] => in_transit
                    [description] => En-route to distribution center hub
                    [carrierStatusCode] => ER00P
                    [carrierDetailCode] =>
                    [signer] =>
                    [location] =>
                )

            [2] => ShipEngine\Model\Package\TrackingEvent Object
                (
                    [dateTime] => ShipEngine\Util\IsoString Object
                        (
                            [value:ShipEngine\Util\IsoString:private] => 2021-05-14T20:00:00.000Z
                        )

                    [carrierDateTime] => ShipEngine\Util\IsoString Object
                        (
                            [value:ShipEngine\Util\IsoString:private] => 2021-05-15T02:00:00
                        )

                    [status] => unknown
                    [description] => Mechanically sorted
                    [carrierStatusCode] => MMSa
                    [carrierDetailCode] => 00004134918400045
                    [signer] =>
                    [location] =>
                )

            [3] => ShipEngine\Model\Package\TrackingEvent Object
                (
                    [dateTime] => ShipEngine\Util\IsoString Object
                        (
                            [value:ShipEngine\Util\IsoString:private] => 2021-05-15T10:00:00.000Z
                        )

                    [carrierDateTime] => ShipEngine\Util\IsoString Object
                        (
                            [value:ShipEngine\Util\IsoString:private] => 2021-05-15T16:00:00
                        )

                    [status] => in_transit
                    [description] => On vehicle for delivery
                    [carrierStatusCode] => OFD-22
                    [carrierDetailCode] => 91R-159E
                    [signer] =>
                    [location] =>
                )

            [4] => ShipEngine\Model\Package\TrackingEvent Object
                (
                    [dateTime] => ShipEngine\Util\IsoString Object
                        (
                            [value:ShipEngine\Util\IsoString:private] => 2021-05-15T19:00:00.000Z
                        )

                    [carrierDateTime] => ShipEngine\Util\IsoString Object
                        (
                            [value:ShipEngine\Util\IsoString:private] => 2021-05-16T01:00:00
                        )

                    [status] => delivered
                    [description] => Delivered
                    [carrierStatusCode] => DV99-0004
                    [carrierDetailCode] =>
                    [signer] => John P. Doe
                    [location] => ShipEngine\Model\Package\Location Object
                        (
                            [cityLocality] =>
                            [stateProvince] =>
                            [postalCode] => 12345
                            [countryCode] =>
                            [latitude] => 39.2271052
                            [longitude] => -111.0306202
                        )

                )

        )

)
```

Continuing with the example at the top, you can also serialize the `TrackPackageResult` Type to a JSON string by using
the `jsonSerialize()` method. View the example below:

```php
... Omitted Code

$trackingRes = $shipengine->trackPackage($trackingData, ['retries' => 2]);

print_r(json_encode($trackingRes, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));  // Return the TrackPackageResult Type as a JSON string.
```

**Successful TrackPackage by Tracking Number and Carrier Code Output:**: This is the `TrackPackageResult` Type serialized as JSON.
```json5
{
    "shipment": {
        "shipmentId": null,
        "carrierAccountID": null,
        "carrierAccount": null,
        "carrier": {
            "name": "FedEx",
            "code": "fedex"
        },
        "estimatedDeliveryDate": "2021-05-18T21:00:00.000Z",
        "actualDeliveryDate": "2021-05-15T19:00:00.000Z"
    },
    "package": {
        "packageId": null,
        "weight": null,
        "dimensions": null,
        "trackingNumber": "abcFedExDelivered",
        "trackingUrl": "https://www.fedex.com/track/abcFedExDelivered"
    },
    "events": [
        {
            "dateTime": "2021-05-13T19:00:00.000Z",
            "carrierDateTime": "2021-05-14T01:00:00",
            "status": "accepted",
            "description": "Dropped-off at shipping center",
            "carrierStatusCode": "ACPT-2",
            "carrierDetailCode": "PU7W",
            "signer": null,
            "location": null
        },
        {
            "dateTime": "2021-05-14T01:00:00.000Z",
            "carrierDateTime": "2021-05-14T07:00:00",
            "status": "in_transit",
            "description": "En-route to distribution center hub",
            "carrierStatusCode": "ER00P",
            "carrierDetailCode": null,
            "signer": null,
            "location": null
        },
        {
            "dateTime": "2021-05-14T20:00:00.000Z",
            "carrierDateTime": "2021-05-15T02:00:00",
            "status": "unknown",
            "description": "Mechanically sorted",
            "carrierStatusCode": "MMSa",
            "carrierDetailCode": "00004134918400045",
            "signer": null,
            "location": null
        },
        {
            "dateTime": "2021-05-15T10:00:00.000Z",
            "carrierDateTime": "2021-05-15T16:00:00",
            "status": "in_transit",
            "description": "On vehicle for delivery",
            "carrierStatusCode": "OFD-22",
            "carrierDetailCode": "91R-159E",
            "signer": null,
            "location": null
        },
        {
            "dateTime": "2021-05-15T19:00:00.000Z",
            "carrierDateTime": "2021-05-16T01:00:00",
            "status": "delivered",
            "description": "Delivered",
            "carrierStatusCode": "DV99-0004",
            "carrierDetailCode": null,
            "signer": "John P. Doe",
            "location": {
                "cityLocality": null,
                "stateProvince": null,
                "postalCode": "12345",
                "countryCode": null,
                "latitude": 39.2271052,
                "longitude": -111.0306202
            }
        }
    ]
}
```
OR you can track by **packageId**

**Successful TrackPackage by PackageId** - This example illustrates how to track a given shipment by `packageId`.
- Instantiate the ShipEngine class.
- Pass `packageId` as a string into the `trackPackage` method.
- Use the `trackPackage` method to track a given shipment using the tracking data in the previous step.
- Print out the result to view tracking data response from ShipEngine API.

```php
<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use ShipEngine\ShipEngine;

$api_key = getenv('SHIPENGINE_api_key');

$shipengine = new ShipEngine($api_key);

$trackingData = 'pkg_1FedExAccepted';

$trackingResult = $shipengine->trackPackage($trackingData, ['retries' => 2]);

print_r($trackingResult);
```
**Successful TrackPackage by PackageId Output:**: As a raw `TrackPackageResult` object.
```php
ShipEngine\Model\Package\TrackPackageResult Object
(
    [shipment] => ShipEngine\Model\Package\Shipment Object
        (
            [shipmentId] => shp_yuh3GkfUjTZSEAQ
            [accountId] => car_kfUjTZSEAQ8gHeT
            [carrierAccount] => ShipEngine\Model\Carriers\CarrierAccount Object
                (
                    [carrier] => ShipEngine\Model\Carriers\Carrier Object
                        (
                            [name] => FedEx
                            [code] => fedex
                        )

                    [accountId] => car_kfUjTZSEAQ8gHeT
                    [accountNumber] => 41E-4928-29314AAX
                    [name] => FedEx Account #1
                )

            [carrier] => ShipEngine\Model\Carriers\Carrier Object
                (
                    [name] => FedEx
                    [code] => fedex
                )

            [estimatedDeliveryDate] => ShipEngine\Util\IsoString Object
                (
                    [value:ShipEngine\Util\IsoString:private] => 2021-05-18T21:00:00.000Z
                )

            [actualDeliveryDate] => ShipEngine\Util\IsoString Object
                (
                    [value:ShipEngine\Util\IsoString:private] => 2021-05-16T13:00:00.000Z
                )

        )

    [package] => ShipEngine\Model\Package\Package Object
        (
            [packageId] => pkg_1FedExAccepted
            [weight] => Array
                (
                    [value] => 76
                    [unit] => kilogram
                )

            [dimensions] => Array
                (
                    [length] => 36
                    [width] => 36
                    [height] => 23
                    [unit] => inch
                )

            [trackingNumber] => 5fSkgyuh3GkfUjTZSEAQ8gHeTU29tZ
            [trackingUrl] => https://www.fedex.com/track/5fSkgyuh3GkfUjTZSEAQ8gHeTU29tZ
        )

    [events] => Array
        (
            [0] => ShipEngine\Model\Package\TrackingEvent Object
                (
                    [dateTime] => ShipEngine\Util\IsoString Object
                        (
                            [value:ShipEngine\Util\IsoString:private] => 2021-05-16T13:00:00.000Z
                        )

                    [carrierDateTime] => ShipEngine\Util\IsoString Object
                        (
                            [value:ShipEngine\Util\IsoString:private] => 2021-05-16T19:00:00
                        )

                    [status] => accepted
                    [description] => Dropped-off at shipping center
                    [carrierStatusCode] => ACPT-2
                    [carrierDetailCode] =>
                    [signer] =>
                    [location] =>
                )

        )

)
```

Continuing with the example at the top, you can also serialize the `TrackPackageResult` Type to a JSON string by using
the `jsonSerialize()` method. View the example below:

```php
... Omitted Code

$trackingRes = $shipengine->trackPackage($trackingData, ['retries' => 2]);

print_r(json_encode($trackingRes, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));  // Return the TrackPackageResult Type as a JSON string.
```

**Successful TrackPackage by PackageId Output:**: This is the `TrackPackageResult` Type serialized as JSON.
```json5
{
    "shipment": {
        "shipmentId": "shp_yuh3GkfUjTZSEAQ",
        "carrierAccountID": "car_kfUjTZSEAQ8gHeT",
        "carrierAccount": {
            "carrier": {
                "name": "FedEx",
                "code": "fedex"
            },
            "accountId": "car_kfUjTZSEAQ8gHeT",
            "accountNumber": "41E-4928-29314AAX",
            "name": "FedEx Account #1"
        },
        "carrier": {
            "name": "FedEx",
            "code": "fedex"
        },
        "estimatedDeliveryDate": "2021-05-18T21:00:00.000Z",
        "actualDeliveryDate": "2021-05-16T13:00:00.000Z"
    },
    "package": {
        "packageId": "pkg_1FedExAccepted",
        "weight": {
            "value": 76,
            "unit": "kilogram"
        },
        "dimensions": {
            "length": 36,
            "width": 36,
            "height": 23,
            "unit": "inch"
        },
        "trackingNumber": "5fSkgyuh3GkfUjTZSEAQ8gHeTU29tZ",
        "trackingUrl": "https://www.fedex.com/track/5fSkgyuh3GkfUjTZSEAQ8gHeTU29tZ"
    },
    "events": [
        {
            "dateTime": "2021-05-16T13:00:00.000Z",
            "carrierDateTime": "2021-05-16T19:00:00",
            "status": "accepted",
            "description": "Dropped-off at shipping center",
            "carrierStatusCode": "ACPT-2",
            "carrierDetailCode": null,
            "signer": null,
            "location": null
        }
    ]
}
```

Exceptions
==========

- This method will only throw an exception that is an instance/extension of
  ([ShipEngineException](../src/Message/ShipEngineException.php)) if there is a problem if a problem occurs, such as a
  network error or an error response from the API.
