<?php declare(strict_types=1);

namespace ShipEngine\Util;

use ShipEngine\Message\ShipEngineError;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class ShipEngineSerializer
{
    private Serializer $serializer;

    public function __construct()
    {
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
    }

    public function deserializeJsonToType(string $json_data, $target_class)
    {
             return $this->serializer->deserialize($json_data, $target_class, 'json');
    }
}
