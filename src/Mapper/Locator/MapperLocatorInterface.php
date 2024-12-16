<?php

namespace Biblioteca\TypesenseBundle\Mapper\Locator;

use Biblioteca\TypesenseBundle\Mapper\Entity\EntityMapperInterface;
use Biblioteca\TypesenseBundle\Mapper\MapperInterface;

interface MapperLocatorInterface
{
    public function has(string $name): bool;

    public function get(string $name): MapperInterface;

    /**
     * @return \Generator<string, MapperInterface>
     */
    public function getMappers(): \Generator;

    public function count(): int;

    /**
     * @template T of object
     *
     * @param class-string<T> $classString
     *
     * @return array<string, EntityMapperInterface<T>>
     */
    public function getEntityMappers(string $classString): array;

    /**
     * @template T of object
     *
     * @param class-string<T> $classString $classString
     */
    public function hasEntityMappers(string $classString): bool;
}
