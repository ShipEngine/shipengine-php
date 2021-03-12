<?php declare(strict_types=1);

namespace ShipEngine\Service\Package;

use ShipEngine\Model\Package\PackageTrackingParams;
use ShipEngine\Model\Package\PackageTrackingResult;
use ShipEngine\Service\AbstractService;
use ShipEngine\Util\ShipEngineSerializer;

final class PackageTrackingService extends AbstractService
{
    // TODO: Implement PackageTrackingService

    public function track(PackageTrackingParams $params)
    {
        $serializer = new ShipEngineSerializer();
        $response = $this->request('package/track', (array)$params);
        $parsed_response = json_decode($response->getBody()->getContents())->result;

        return $serializer->deserializeJsonToType($parsed_response, PackageTrackingResult::class);
    }
}
