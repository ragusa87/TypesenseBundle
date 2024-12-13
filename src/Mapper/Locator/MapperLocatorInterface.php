<?php

namespace Biblioteca\TypesenseBundle\Mapper\Locator;

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
}
