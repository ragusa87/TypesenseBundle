<?php

namespace Biblioteca\TypesenseBundle\Mapper\Entity;

use Biblioteca\TypesenseBundle\Mapper\Converter\ValueConverterInterface;
use Biblioteca\TypesenseBundle\Mapper\Converter\ValueExtractorInterface;
use Biblioteca\TypesenseBundle\Mapper\MappingGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @template T of Object
 *
 * @implements EntityTransformerInterface<T>
 */
final class EntityTransformer implements EntityTransformerInterface
{
    public function __construct(
        readonly EntityManagerInterface $entityManager,
        readonly ValueConverterInterface $valueConverter,
        readonly ValueExtractorInterface $valueExtractor,
        readonly MappingGeneratorInterface $mappingGenerator,
    ) {
    }

    public function transform(object $entity): array
    {
        $data = [];

        foreach ($this->mappingGenerator->getMapping()->getFields() as $fieldMapping) {
            $fieldName = $fieldMapping->getEntityAttribute() ?? $fieldMapping->getName();
            $value = $this->valueExtractor->getValue($entity, $fieldName);

            $data[$fieldMapping->getName()] = $this->valueConverter->convert($value, $fieldMapping->getType(), $fieldMapping->isOptional());
        }

        return $data;
    }

    public function support(object $entity): bool
    {
        return true;
    }
}
