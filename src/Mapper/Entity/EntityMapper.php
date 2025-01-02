<?php

namespace Biblioteca\TypesenseBundle\Mapper\Entity;

use Biblioteca\TypesenseBundle\Mapper\Converter\Exception\ValueConversionException;
use Biblioteca\TypesenseBundle\Mapper\Converter\Exception\ValueExtractorException;
use Biblioteca\TypesenseBundle\Mapper\Converter\ValueConverterInterface;
use Biblioteca\TypesenseBundle\Mapper\Converter\ValueExtractorInterface;
use Biblioteca\TypesenseBundle\Mapper\Fields;
use Biblioteca\TypesenseBundle\Mapper\Fields\FieldMapping;
use Biblioteca\TypesenseBundle\Mapper\Mapping\Mapping;
use Biblioteca\TypesenseBundle\Mapper\Metadata\MetadataMapping;
use Biblioteca\TypesenseBundle\Mapper\Options\CollectionOptions;
use Biblioteca\TypesenseBundle\Type\DataTypeEnum;
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
     * @param array{metadata: array<string, mixed>, fields: array<string, FieldMappingArray>, token_separators: list<string>, symbols_to_index: list<string>, default_sorting_field: string|null } $mappingConfig
     * @param class-string<T>                                                                                                                                                                      $className
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
        $mapping = new Mapping(metadataMapping: $this->getMetadataMapping(), collectionOptions: $this->getCollectionOptions());
        if (false === $this->hasIdField()) {
            $mapping->add('id', DataTypeEnum::STRING->value);
        }

        $this->checkIdType();
        foreach ($this->mappingConfig['fields'] as $config) {
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

    private function getCollectionOptions(): ?CollectionOptions
    {
        $collectionOption = $this->mappingConfig;
        unset($collectionOption['fields']);
        unset($collectionOption['metadata']);
        foreach ($collectionOption as $key => $value) {
            if ($key === 'default_sorting_field' || !is_array($value)) {
                continue;
            }
            $collectionOption[$key] = $value === [] ? null : array_map(strval(...), $value);
        }

        if (array_filter($collectionOption, fn ($value) => $value !== null) === []) {
            return null;
        }

        return CollectionOptions::fromArray($collectionOption);
    }

    private function getMetadataMapping(): ?MetadataMapping
    {
        if ($this->mappingConfig['metadata'] === []) {
            return null;
        }

        return new MetadataMapping($this->mappingConfig['metadata']);
    }

    private function hasIdField(): bool
    {
        foreach ($this->mappingConfig['fields'] as $field) {
            if (($field['name'] ?? '') === 'id') {
                return true;
            }
        }

        return true;
    }

    /**
     * @throws \InvalidArgumentException if the id field is not of type string
     */
    private function checkIdType(): void
    {
        foreach ($this->mappingConfig['fields'] as $field) {
            if (($field['name'] ?? '') === 'id' && ($field['type'] ?? '') !== DataTypeEnum::STRING->value) {
                throw new \InvalidArgumentException('The id field must be of type string');
            }
        }
    }
}
