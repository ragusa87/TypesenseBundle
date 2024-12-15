<?php

namespace Biblioteca\TypesenseBundle\Mapper\Mapping;

use Biblioteca\TypesenseBundle\Mapper\Fields\FieldMapping;
use Biblioteca\TypesenseBundle\Mapper\Fields\FieldMappingInterface;
use Biblioteca\TypesenseBundle\Mapper\Metadata\MetadataMappingInterface;
use Biblioteca\TypesenseBundle\Mapper\Options\CollectionOptionsInterface;
use Biblioteca\TypesenseBundle\Type\DataTypeEnum;

class Mapping implements MappingInterface
{
    public function __construct(
        /** @var array<int, FieldMappingInterface> */
        private array $fields = [],
        private readonly ?MetadataMappingInterface $metadataMapping = null,
        private readonly ?CollectionOptionsInterface $collectionOptions = null,
    ) {
    }

    /**
     * @return FieldMappingInterface[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function addField(FieldMappingInterface $fieldMapping): self
    {
        $this->fields[] = $fieldMapping;

        return $this;
    }

    public function add(string $name, DataTypeEnum $dataTypeEnum, ?bool $facet = null, ?bool $optional = null): self
    {
        $this->addField(new FieldMapping(name: $name, type: $dataTypeEnum, facet: $facet, optional: $optional));

        return $this;
    }

    public function getCollectionOptions(): ?CollectionOptionsInterface
    {
        return $this->collectionOptions;
    }

    public function getMetadata(): ?MetadataMappingInterface
    {
        return $this->metadataMapping;
    }
}
