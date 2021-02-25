<?php


namespace ShipEngine\Service;


use Psr\Http\Message\ResponseInterface;

trait TagsTrait
{
    public function createTag(string $method, array $params): ResponseInterface
    {
        return $this->tag->createTagRequest($method, $params);
    }
}