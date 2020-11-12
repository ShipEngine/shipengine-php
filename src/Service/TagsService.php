<?php declare(strict_types=1);

namespace ShipEngine\Service;

final class TagsService extends AbstractService
{
    public function create(string $tag)
    {
        $response = $this->request('POST', '/tags/' . $tag);
        $body = (string) $response->getBody();
        $data = json_decode($body, true);

        return $data['name'];
    }
}
