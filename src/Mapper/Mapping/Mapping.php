<?php

namespace Biblioverse\TypesenseBundle\Mapper\Mapping;

use Biblioverse\TypesenseBundle\Mapper\Fields\FieldMapping;
use Biblioverse\TypesenseBundle\Mapper\Fields\FieldMappingInterface;
use Biblioverse\TypesenseBundle\Mapper\Metadata\MetadataMappingInterface;
use Biblioverse\TypesenseBundle\Mapper\Options\CollectionOptionsInterface;
use Biblioverse\TypesenseBundle\Type\DataTypeEnum;

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

    public function add(string $name, string|DataTypeEnum $type, ?bool $facet = null, ?bool $optional = null): self
    {
        $this->addField(new FieldMapping(name: $name, type: $type, facet: $facet, optional: $optional));

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
