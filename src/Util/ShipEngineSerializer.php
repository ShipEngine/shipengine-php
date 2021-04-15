<?php declare(strict_types=1);

namespace ShipEngine\Util;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * ShipEngineSerializer to deserialize JSON strings to our PHP Type objects,
 * and serialize our PHP Type objects into JSON strings.
 *
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
     *
     * @throws \Symfony\Component\Serializer\Exception\InvalidArgumentException
     */
    public function __construct()
    {
        $encoder = [new JsonEncoder()];
        $normalizer = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizer, $encoder);
    }

    /**
     * Deserialize a JSON string into a PHP Type object.
     *
     * @param string $json_data
     * @param string $target_class
     * @return mixed
     * @throws NotEncodableValueException
     */
    public function deserializeJsonToType(string $json_data, string $target_class)
    {
        return $this->serializer->deserialize($json_data, $target_class, 'json');
    }

    /**
     * Serialize a PHP object into a specific PHP Object/Type.
     *
     * @param mixed $php_object
     * @param string $target_class
     * @return mixed
     * @throws NotEncodableValueException
     */
    public function serializeDataToType($php_object, string $target_class)
    {
        $json = $this->serializeDataToJson($php_object);
        return $this->deserializeJsonToType($json, $target_class);
    }

    /**
     * Serialize arbitrary PHP data objects (e.g. an array or explicit object) into JSON strings.
     *
     * @param mixed $data
     * @return string
     * @throws NotEncodableValueException
     */
    public function serializeDataToJson($data): string
    {
        return $this->serializer->serialize($data, 'json');
    }
}
