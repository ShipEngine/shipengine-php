<?php declare(strict_types=1);

namespace ShipEngine\Service\Package;

use Psr\Http\Client\ClientExceptionInterface;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\Model\Package\TrackingQuery;
use ShipEngine\Model\Package\TrackPackageResult;
use ShipEngine\ShipEngineClient;
use ShipEngine\ShipEngineConfig;
use ShipEngine\Util\Constants\RPCMethods;

/**
 * Track a given package to obtain status updates on it's progression through the fulfillment cycle.
 *
 * <br>
 * **Usage**:
 * ```php
 * $tracking_service = new TrackingService();
 * $tracking_service->track(args);
 * ```
 *
 * @package ShipEngine\Service\Package
 */
final class TrackPackageService
{
    /**
     * Track a package by `tracking_number` and `carrier_code` via the **TrackingQuery** object, or ou can track by
     * passing in a string that is the **package_id** of the shipment you wish to track.
     *
     * <br>
     * **Ways to track**:
     * - By tracking number and carrier code: pass in an instance of **TrackingQuery** which has properties for
     * **tracking_number** and **carrier_code**.
     * - By package id: pass in a **string** that is the **package_id** of the shipment you wish to track.
     *
     * @param ShipEngineConfig $config ShipEngine configuration object.
     * @return TrackPackageResult
     * @throws ClientExceptionInterface
     */
    public function track(
        ShipEngineConfig $config,
        $tracking_data
    ): TrackPackageResult {
        $client = new ShipEngineClient();

        if (is_string($tracking_data)) {
            $api_response = $client->request(
                RPCMethods::TRACK_PACKAGE,
                $config,
                array('package_id' => $tracking_data)
            );
            return new TrackPackageResult($api_response);
        } elseif ($tracking_data instanceof TrackingQuery) {
            $api_response = $client->request(
                RPCMethods::TRACK_PACKAGE,
                $config,
                $tracking_data->jsonSerialize()
            );
            return new TrackPackageResult($api_response);
        }
        throw new ShipEngineException('Could not track package with the arguments provided.');
    }
}
