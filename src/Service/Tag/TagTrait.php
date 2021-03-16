<?php declare(strict_types=1);

namespace ShipEngine\Service\Tag;

use ShipEngine\Model\Tag\Tag;

/**
 * Convenience method to create a tag.
 *
 * @package @package ShipEngine\Service\Tag
 */
trait TagTrait
{
    /**
     * A convenience method to `create a tag` via the *tag/create* remote procedure.
     *
     * @param string $tag
     * @return Tag
     * @see \ShipEngine\Service\Tag\TagService::create()
     */
    public function createTag(string $tag): Tag
    {
        $parameters = array('name' => $tag);
        return $this->tags->create($parameters);
    }
}
