<?php


namespace ShipEngine\Service;


use Psr\Http\Message\ResponseInterface;
use ShipEngine\ShipEngineError;

trait TagsTrait
{
    /**
     * Make a `create_tag` RPC request.
     *
     * @param string $tag
     * @return string
     * @see \ShipEngine\Service\TagsService::create()
     */
    public function createTag(string $tag): string
    {
        try {
            $parameters = array('name' => $tag);
            return $this->tags->create($parameters)->name;
        } catch (ShipEngineError $e) {
            echo $e->getMessage();
        }

    }
}