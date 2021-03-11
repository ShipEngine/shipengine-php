<?php declare(strict_types=1);

namespace ShipEngine\Service\Package;

use ShipEngine\Model\Package\PackageTrackingParams;

trait PackageTrackingTrait
{
    public function trackPackage(string $tracking_number, string $carrier_code): TrackingData
    {
        $package_track_params = new PackageTrackingParams(
            $tracking_number,
            $carrier_code
        );

        $result = $this->tracking->track($package_track_params);

        return new TrackingData(); // TODO: implement the build of this from the result data above.
    }
}
