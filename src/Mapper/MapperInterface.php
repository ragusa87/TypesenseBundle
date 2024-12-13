<?php

namespace Biblioteca\TypesenseBundle\Mapper;

use Biblioteca\TypesenseBundle\Mapper\Mapping\MappingInterface;

interface MapperInterface
{
    public const TAG_NAME = 'biblioteca_typesense.mapper';

    public static function getName(): string;

    public function getMapping(): MappingInterface;

    /**
     * Data to index, the key is the field name
     * @return \Generator<array<string, mixed>>
     */
    public function getData(): \Generator;

    /**
     * How many data to index. If null, the progression is unknown.
     * @return int|null
     */
    public function getDataCount(): ?int;
}
