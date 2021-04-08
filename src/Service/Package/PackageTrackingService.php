<?php declare(strict_types=1);

namespace ShipEngine\Service\Package;

use ShipEngine\Message\ShipEngineException;
use ShipEngine\Model\Package\PackageTrackingParams;
use ShipEngine\Model\Package\PackageTrackingResult;
use ShipEngine\Service\ShipEngineConfig;
use ShipEngine\ShipEngineClient;
use ShipEngine\Util\RPCMethods;
use ShipEngine\Util\ShipEngineSerializer;

/**
 * Track a single package via the `package/track` remote procedure.
 *
 * @package ShipEngine\Service\Package
 */
final class PackageTrackingService
{
    private ShipEngineClient $client;

    /**
     * Track a single package via the `package/track` remote procedure.
     *
     * @param PackageTrackingParams $params
     * @param ShipEngineConfig $config
     * @return PackageTrackingResult
     */
    public function track(PackageTrackingParams $params, ShipEngineConfig $config): PackageTrackingResult
    {
        $serializer = new ShipEngineSerializer();
        $response = $this->client->request(RPCMethods::PACKAGE_TRACK, (array)$params, $config);

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
