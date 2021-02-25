<?php


namespace ShipEngine\Service;



use Psr\Http\Message\ResponseInterface;

class TagsService extends AbstractService
{
    public function createTagRequest(string $method, array $params): ResponseInterface
    {
        return $this->request($method, $params);
    }
}