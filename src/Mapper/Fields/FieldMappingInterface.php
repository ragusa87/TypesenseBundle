<?php

namespace Biblioteca\TypesenseBundle\Mapper\Fields;

use Biblioteca\TypesenseBundle\Type\DataTypeEnum;

interface FieldMappingInterface
{
    /**
     * Field options and value.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array;

    /**
     * @see DataTypeEnum
     */
    public function getType(): string;

    public function getName(): string;
}
