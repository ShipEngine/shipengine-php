<?php declare(strict_types=1);

namespace ShipEngine\Service\Tag;

use ShipEngine\Model\Tag\Tag;
use ShipEngine\Service\AbstractService;
use ShipEngine\ShipEngineError;

/**
 * Service to create tags.
 */
class TagService extends AbstractService
{
    /**
     * Make a `tag/create` RPC request.
     *
     * @param array $params
     * @return Tag
     * @throws ShipEngineError if a tag cannot be created.
     */
    public function create(array $params): Tag
    {
        $response = $this->request('tag/create', $params);
        $parsed_response = json_decode($response->getBody()->getContents(), true);

        if (empty($parsed_response['result'][0]['messages']['errors'])) {
            return new Tag(
                $parsed_response['result']['name'],
            );
        }

//        $errors = array();
//        foreach ($parsed_response['result'][0]['messages']['errors'] as $error) {
//            $errors[] = $error;
//        }

        throw new ShipEngineError('Faild to create the provided.');
    }
}
