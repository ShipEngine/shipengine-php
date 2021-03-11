<?php declare(strict_types=1);

namespace ShipEngine\Service\Package;

use ShipEngine\Model\Package\PackageTrackingParams;
use ShipEngine\Model\Package\PackageTrackingResult;
use ShipEngine\Service\AbstractService;

final class PackageTrackingService extends AbstractService
{
    // TODO: Implement PackageTrackingService

    public function track(PackageTrackingParams $params)
    {
        $response = $this->request('package/track', (array)$params);
        $parsed_response = json_decode($response->getBody()->getContents())->result;

        return $this->deserializeJsonToType($parsed_response, PackageTrackingResult::class);
    }
}
