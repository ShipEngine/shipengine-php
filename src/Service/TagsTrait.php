<?php declare(strict_types=1);

namespace ShipEngine\Service;

/**
 * Convenience method to create a tag.
 */
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
        $parameters = array('name' => $tag);
        return $this->tags->create($parameters)->name;
    }
}
