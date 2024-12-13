<?php

namespace Biblioteca\TypesenseBundle\Mapper\Mapping;

use Biblioteca\TypesenseBundle\Mapper\Fields\FieldMappingInterface;
use Biblioteca\TypesenseBundle\Mapper\Metadata\MetadataMappingInterface;
use Biblioteca\TypesenseBundle\Mapper\Options\CollectionOptionsInterface;

interface MappingInterface
{
    /**
     * @return FieldMappingInterface[]
     */
    public function getFields(): array;

    public function getCollectionOptions(): ?CollectionOptionsInterface;

    public function getMetadata(): ?MetadataMappingInterface;
}
