<?php declare(strict_types=1);

namespace ShipEngine\Service\Package;

use ShipEngine\Message\ShipEngineError;
use ShipEngine\Model\Package\PackageTrackingParams;
use ShipEngine\Model\Package\TrackingData;
use ShipEngine\Util\ShipEngineSerializer;

/**
 * Convenience method to obtain `tracking data` for a single package.
 */
trait PackageTrackingTrait
{
    /**
     * A method to get `tracking data` via the *package/track* remote procedure.
     *
     * @param string $tracking_number
     * @param string $carrier_code
     * @return TrackingData
     * @throws ShipEngineError
     */
    public function trackPackage(string $tracking_number, string $carrier_code): TrackingData
    {
        $serializer = new ShipEngineSerializer();

        $package_track_params = new PackageTrackingParams(
            $tracking_number,
            $carrier_code
        );

        $result = $this->tracking->track($package_track_params);


        $returnValue = $serializer->deserializeJsonToType(
            $result->jsonSerialize(),
            TrackingData::class
        );

        if (!$returnValue->messages['errors']) {
            return $returnValue;
        } else {
            $errors = $returnValue->messages['errors'];
            $error_string = '';
            foreach ($errors as $error) {
                $error_string = $error;
            }
            throw new ShipEngineError($error_string);
        }
    }
}
