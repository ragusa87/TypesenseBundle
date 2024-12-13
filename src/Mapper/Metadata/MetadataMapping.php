<?php

namespace Biblioteca\TypesenseBundle\Mapper\Metadata;

use Biblioteca\TypesenseBundle\Utils\ArrayAccessTrait;

/**
 * @implements \ArrayAccess<string, mixed>
 */
class MetadataMapping implements MetadataMappingInterface, \ArrayAccess, \IteratorAggregate
{
    use ArrayAccessTrait;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
