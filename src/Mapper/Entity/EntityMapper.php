<?php

namespace Biblioteca\TypesenseBundle\Mapper\Entity;

use Biblioteca\TypesenseBundle\Mapper\Converter\Exception\ValueConversionException;
use Biblioteca\TypesenseBundle\Mapper\Converter\Exception\ValueExtractorException;
use Biblioteca\TypesenseBundle\Mapper\Converter\ValueConverterInterface;
use Biblioteca\TypesenseBundle\Mapper\Converter\ValueExtractorInterface;
use Biblioteca\TypesenseBundle\Mapper\Fields;
use Biblioteca\TypesenseBundle\Mapper\Fields\FieldMapping;
use Biblioteca\TypesenseBundle\Mapper\Mapping\Mapping;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @template T of Object
 *
 * @extends AbstractEntityMapper<T>
 *
 * @phpstan-import-type FieldMappingArray from Fields\FieldMapping
 */
final class EntityMapper extends AbstractEntityMapper
{
    /**
     * @param array{fields: array<string, FieldMappingArray>} $mappingConfig
     * @param class-string<T>                                 $className
     */
    public function __construct(
        readonly EntityManagerInterface $entityManager,
        readonly ValueConverterInterface $valueConverter,
        readonly ValueExtractorInterface $valueExtractor,
        private readonly string $className,
        private readonly array $mappingConfig,
    ) {
        parent::__construct($entityManager);
    }

    public function getMapping(): Mapping
    {
        $mapping = new Mapping();
        $mapping->add('id', 'string');
        foreach ($this->mappingConfig['fields'] as $name => $config) {
            $config['name'] = $name;
            $mapping->addField(FieldMapping::fromArray($config));
        }

        return $mapping;
    }

    /**
     * @throws ValueConversionException
     * @throws ValueExtractorException
     */
    public function transform(object $entity): array
    {
        $data = [];

        foreach ($this->getMapping()->getFields() as $fieldMapping) {
            $fieldName = $fieldMapping->getEntityAttribute() ?? $fieldMapping->getName();
            $value = $this->valueExtractor->getValue($entity, $fieldName);

            $data[$fieldMapping->getName()] = $this->valueConverter->convert($value, $fieldMapping->getType(), $fieldMapping->isOptional());
        }

        return $data;
    }

    public function getClassName(): string
    {
        return $this->className;
    }
}
