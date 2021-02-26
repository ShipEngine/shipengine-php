<?php declare(strict_types=1);

namespace ShipEngine\Service;

use ShipEngine\Model\Tags\Tag;
use ShipEngine\ShipEngineError;

/**
 * Service to create tags.
 */
final class TagsService extends AbstractService
{
    /**
     * Make a `create_tag` RPC request.
     *
     * @param array $params
     * @return Tag
     * @throws ShipEngineError if a tag cannot be created.
     */
    public function create(array $params): Tag
    {
        $response = $this->request('create_tag', $params);
        $parsed_response = json_decode((string)$response->getBody());

        return new Tag(
            $parsed_response->name,
        );
    }
}
