<?php

namespace Biblioteca\TypesenseBundle\Mapper\Locator;

use Biblioteca\TypesenseBundle\Mapper\EntityMapperInterface;
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
     * @return \Generator<int, EntityMapperInterface<T>>
     */
    public function getEntityMappers(string $classString): \Generator;
}
