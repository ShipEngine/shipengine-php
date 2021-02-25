<?php


namespace ShipEngine\Service;

use Psr\Http\Message\ResponseInterface;

use ShipEngine\Model\Tags\Tag;
use ShipEngine\ShipEngineError;

/*
 * Service to create tags
 */
final class TagsService extends AbstractService
{
    private const CREATE = 'create_tag';

    /**
     * Make a `create_tag` RPC request.
     *
     * @param array $params
     * @return Tag
     * @throws ShipEngineError if a tag cannot be created.
     */
    public function create(array $params): Tag
    {
        if (is_array($params)) {
            $response = $this->request(self::CREATE, $params);
            $parsed_response = json_decode((string)$response->getBody());

            return new Tag(
                $parsed_response->name,
            );
        } else {
            throw new ShipEngineError('Could not create tag, `$params` must be an array');
        }
    }
}