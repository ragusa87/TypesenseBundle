<?php

namespace Biblioverse\TypesenseBundle\Mapper\Entity;

use Biblioverse\TypesenseBundle\Mapper\Fields\FieldMapping;
use Biblioverse\TypesenseBundle\Mapper\Mapping\Mapping;
use Biblioverse\TypesenseBundle\Mapper\MappingGeneratorInterface;
use Biblioverse\TypesenseBundle\Mapper\Metadata\MetadataMapping;
use Biblioverse\TypesenseBundle\Mapper\Options\CollectionOptions;
use Biblioverse\TypesenseBundle\Type\DataTypeEnum;

/**
 * @phpstan-import-type FieldMappingArray from FieldMapping
 *
 * @phpstan-type MappingConfiguration array{metadata: array<string, mixed>, fields: array<int, FieldMappingArray>, token_separators: list<string>, symbols_to_index: list<string>, default_sorting_field: string|null }
 */
final class EntityMapperGenerator implements MappingGeneratorInterface
{
    /**
     * @param MappingConfiguration $mappingConfig
     */
    public function __construct(
        private readonly array $mappingConfig,
    ) {
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

        return false;
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
