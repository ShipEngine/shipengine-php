<?php declare(strict_types=1);

namespace ShipEngine\Service\Package;

use ShipEngine\Message\ShipEngineException;
use ShipEngine\Model\Package\PackageTrackingParams;
use ShipEngine\Model\Package\PackageTrackingResult;
use ShipEngine\Service\AbstractService;
use ShipEngine\Util\ShipEngineSerializer;

/**
 * Track a single package via the `package/track` remote procedure.
 *
 * @package ShipEngine\Service\Package
 */
final class PackageTrackingService extends AbstractService
{
    /**
     * Track a single package via the `package/track` remote procedure.
     *
     * @param PackageTrackingParams $params
     * @return PackageTrackingResult
     */
    public function track(PackageTrackingParams $params): PackageTrackingResult
    {
        $serializer = new ShipEngineSerializer();
        $response = $this->request('package/track', (array)$params);

        $status_code = $response->getStatusCode();
        $reason_phrase = $response->getReasonPhrase();

        if ($status_code !== 200) {
            throw new ShipEngineException(
                "Validation request failed -- status_code: {$status_code} reason: {$reason_phrase}"
            );
        }

        $parsed_response = json_decode($response->getBody()->getContents());

        return $serializer->deserializeJsonToType(
            json_encode($parsed_response->result),
            PackageTrackingResult::class
        );
    }
}
