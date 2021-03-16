<?php declare(strict_types=1);

namespace ShipEngine\Util;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class ShipEngineSerializer
 * @package ShipEngine\Util
 */
final class ShipEngineSerializer
{
    /**
     * @var Serializer
     */
    private Serializer $serializer;

    /**
     * ShipEngineSerializer constructor.
     * @throws \Symfony\Component\Serializer\Exception\InvalidArgumentException
     */
    public function __construct()
    {
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
    }

    public function deserializeJsonToType(string $json_data, $target_class)
    {
        return $this->serializer->deserialize($json_data, $target_class, 'json');
    }

    public function serializeDataToType($php_object, $target_class)
    {
        $json = $this->serializeDataToJson($php_object);
        return $this->deserializeJsonToType($json, $target_class);
    }

    public function serializeDataToJson($data): string
    {
        return $this->serializer->serialize($data, 'json');
    }
}
