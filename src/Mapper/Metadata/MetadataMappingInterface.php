<?php

namespace Biblioteca\TypesenseBundle\Mapper\Metadata;

interface MetadataMappingInterface
{
    /**
     * Metadata options and value.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array;
}
