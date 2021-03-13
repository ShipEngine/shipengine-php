<?php declare(strict_types=1);

namespace ShipEngine\Service\Tag;

use ShipEngine\Message\ShipEngineError;
use ShipEngine\Model\Tag\Tag;
use ShipEngine\Service\AbstractService;
use ShipEngine\Util\ShipEngineSerializer;

/**
 * Service to create tags.
 */
class TagService extends AbstractService
{
    /**
     * Make a `tag/create` RPC request.
     *
     * @param array $params
     * @return Tag
     * @throws ShipEngineError if a tag cannot be created.
     */
    public function create(array $params): Tag
    {
        $serializer = new ShipEngineSerializer();
        $response = $this->request('tag/create', $params);
        $status_code = $response->getStatusCode();
        $reason_phrase = $response->getReasonPhrase();

        if ($response->getStatusCode() != 200) {
            throw new ShipEngineError(
                "Validation request failed -- status_code: {$status_code} reason: {$reason_phrase}"
            );
        }

        $parsed_response = json_decode($response->getBody()->getContents());

        return $serializer->deserializeJsonToType(json_encode($parsed_response->result), Tag::class);
    }
}
