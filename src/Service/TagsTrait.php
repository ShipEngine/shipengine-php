<?php


namespace ShipEngine\Service;


trait TagsTrait
{
    public function createTag(string $method, array $params)
    {
        $this->tag->createTagRequest($method, $params);
    }
}