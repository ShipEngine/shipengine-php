<?php declare(strict_types=1);

namespace ShipEngine\Service\Tag;

use ShipEngine\Model\Tag\Tag;
use ShipEngine\Service\AbstractService;
use ShipEngine\ShipEngineError;

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
     * @throws ShipEngineError if a tag cannot be created.  TODO: Implement throw ShipEngineError if request fails.
     */
    public function create(array $params): Tag
    {
        $response = $this->request('tag/create', $params);
        $parsed_response = json_decode($response->getBody()->getContents(), true);

        return new Tag(
            $parsed_response['result']['name'],
        );
    }
}
