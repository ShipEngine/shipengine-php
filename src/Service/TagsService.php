<?php


namespace ShipEngine\Service;

use Psr\Http\Message\ResponseInterface;

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
     * @return ResponseInterface
     */
    public function create(array $params): ResponseInterface
    {
        return $this->request(self::CREATE, $params);
    }
}