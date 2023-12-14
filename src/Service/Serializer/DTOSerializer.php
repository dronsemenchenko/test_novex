<?php

namespace App\Service\Serializer;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;

class DTOSerializer implements SerializerInterface
{
    private SerializerInterface $serializer;

    public function __construct()
    {
        $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());

        $this->serializer = new Serializer(
            [new DateTimeNormalizer(), $normalizer],
            [new JsonEncoder()]
        );
    }

    public function serialize(mixed $data, string $format, array $context = []): string
    {
        return $this->serializer->serialize($data, $format, $context);
    }

    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed
    {
        return $this->serializer->deserialize($data, $type, $format, $context);
    }

}
