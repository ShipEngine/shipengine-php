<?php declare(strict_types=1);

namespace ShipEngine\Service\Package;

use Psr\Http\Client\ClientExceptionInterface;
use ShipEngine\Message\ShipEngineException;
use ShipEngine\Model\Package\Package;
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
     * Track a package by `tracking_number` and `carrier_code` via the **TrackingQuery** object, by using just the
     * **package_id**, or by using a **Package** object.
     *
     * <br>
     * **Ways to track**:
     * - By tracking number and carrier code
     * - By package id
     * - By Package object
     *
     * @param ShipEngineConfig $config ShipEngine configuration object.
     * @param TrackingQuery|null $arg1 TrackingQuery object which has tracking_number and carrier_code as properties.
     * Use this to track a shipment by tracking number and carrier_code.
     * @param string|null $arg2 package id - Use this to track a shipment by package_id.
     * @param Package|null $arg3 Package object - Use this to track a shipment by Package object.
     * @return TrackPackageResult
     * @throws ClientExceptionInterface
     */
    public function track(
        ShipEngineConfig $config,
        ?TrackingQuery $arg1 = null,
        ?string $arg2 = null,
        ?Package $arg3 = null
    ): TrackPackageResult {
        $client = new ShipEngineClient();

        // Checking if user wants to track by package_id (verify that $arg2 is not null)
        if ($arg1 === null && $arg2 !== null && $arg3 === null) {
            $api_response = $client->request(
                RPCMethods::TRACK_PACKAGE,
                $config,
                array('package_id' => $arg2)
            );
            return new TrackPackageResult($api_response);

        // Checking if user wants to track by tracking_number and carrier_code (verify that $arg1 is not null
            // and is an instance of TrackingQuery)
        } elseif (($arg1 !== null && $arg2 === null && $arg3 === null) && $arg1 instanceof TrackingQuery) {
            $api_response = $client->request(
                RPCMethods::TRACK_PACKAGE,
                $config,
                $arg1->jsonSerialize()
            );
            return new TrackPackageResult($api_response);

        // Checking if user wants to track by tracking_number and carrier_code (verify that $arg3 is not null
            // and is an instance of Package)
        } elseif (($arg1 === null && $arg2 === null && $arg3 !== null) && $arg3 instanceof Package) {
            $api_response = $client->request(
                RPCMethods::TRACK_PACKAGE,
                $config,
                $arg3->jsonSerialize()
            );
            return new TrackPackageResult($api_response);
        } else { //TODO: check if this is best way to manage this if block.
            throw new ShipEngineException('Could not track package with the arguments provided.');
        }
    }
}
