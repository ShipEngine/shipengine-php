<?php declare(strict_types=1);

namespace ShipEngine\Service;

trait TagsTrait
{
    public function createTag(string $tag) {
        return $this->tags->create($tag);
    }
}
