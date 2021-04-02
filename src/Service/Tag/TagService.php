<?php declare(strict_types=1);

namespace ShipEngine\Service\Tag;

use ShipEngine\Message\ShipEngineException;
use ShipEngine\Model\Tag\Tag;
use ShipEngine\Service\AbstractService;
use ShipEngine\Service\ShipEngineConfig;
use ShipEngine\Util\ShipEngineSerializer;

/**
 * Service to create tags.
 *
 * @package ShipEngine\Service\Tag
 */
class TagService extends AbstractService
{
    /**
     * Make a `tag/create` RPC request.
     *
     * @param array $params
     * @param ShipEngineConfig $config
     * @return Tag
     */
    public function create(array $params, ShipEngineConfig $config): Tag
    {
        $serializer = new ShipEngineSerializer();
        $response = $this->request('tag/create', $params, $config);
        $status_code = $response->getStatusCode();
        $reason_phrase = $response->getReasonPhrase();

        if ($response->getStatusCode() != 200) {
            throw new ShipEngineException(
                "Validation request failed -- status_code: {$status_code} reason: {$reason_phrase}"
            );
        }

        $parsed_response = json_decode($response->getBody()->getContents());

        return $serializer->deserializeJsonToType(json_encode($parsed_response->result), Tag::class);
    }
}
