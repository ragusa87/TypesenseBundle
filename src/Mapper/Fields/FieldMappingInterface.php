<?php

namespace Biblioteca\TypesenseBundle\Mapper\Fields;

use Biblioteca\TypesenseBundle\Type\DataTypeEnum;

interface FieldMappingInterface
{
    /**
     * Field options and value. This is the raw data that will be sent to Typesense.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array;

    /**
     * @see DataTypeEnum
     */
    public function getType(): string;

    /**
     * Field name.
     */
    public function getName(): string;

    /**
     * Name of the field in the entity. Can be composed (Ex: `user.name`).
     * You should use `name` if not set.
     */
    public function getEntityAttribute(): ?string;

    public function isOptional(): bool;
}
