<?php

namespace Biblioteca\TypesenseBundle\Mapper\Fields;

use Biblioteca\TypesenseBundle\Type\DataTypeEnum;

class FieldMapping implements FieldMappingInterface
{
    public string $type;

    public function __construct(
        public string $name,
        DataTypeEnum|string $type,
        public ?bool $facet = null,
        public ?bool $optional = null,
        public ?bool $drop = null,
        public ?bool $index = null,
        public ?bool $infix = null,
        public ?bool $rangeIndex = null,
        public ?bool $sort = null, // Default depends on the type; not assigned here
        public ?bool $stem = null,
        public ?bool $store = null,
        public ?int $numDim = null,
        public ?string $locale = null,
        public ?string $reference = null,
        public ?string $vecDist = null,
    ) {
        $this->type = $type instanceof DataTypeEnum ? $type->value : $type;
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'type' => $this->type,
            'facet' => $this->facet,
            'optional' => $this->optional,
            'index' => $this->index,
            'store' => $this->store,
            'sort' => $this->sort,
            'infix' => $this->infix,
            'locale' => $this->locale,
            'num_dim' => $this->numDim,
            'vec_dist' => $this->vecDist,
            'reference' => $this->reference,
            'range_index' => $this->rangeIndex,
            'drop' => $this->drop,
            'stem' => $this->stem,
        ], fn ($value) => $value !== null);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
