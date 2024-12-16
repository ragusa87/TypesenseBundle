<?php

namespace Biblioteca\TypesenseBundle\Mapper\Entity;

use Biblioteca\TypesenseBundle\Mapper\MapperInterface;

/**
 * @template T of object
 */
interface EntityMapperInterface extends MapperInterface
{
    /**
     * @param object&T $entity
     *
     * @return array<string, mixed>
     */
    public function transform(object $entity): array;

    /**
     * @param object&T $entity
     */
    public function support(object $entity): bool;
}
