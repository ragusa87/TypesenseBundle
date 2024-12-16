<?php

namespace Biblioteca\TypesenseBundle\Mapper;

/**
 * Implement this interface if you want to create a mapper not attached to any entity.
 */
interface StandaloneMapperInterface extends MapperInterface
{
    public const TAG_NAME = 'biblioteca_typesense.mapper';

    public static function getName(): string;
}
