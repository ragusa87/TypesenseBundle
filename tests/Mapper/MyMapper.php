<?php

namespace Biblioteca\TypesenseBundle\Tests\Mapper;

use Biblioteca\TypesenseBundle\Mapper\MapperInterface;
use Biblioteca\TypesenseBundle\Mapper\Mapping\Mapping;
use Biblioteca\TypesenseBundle\Mapper\Mapping\MappingInterface;
use Biblioteca\TypesenseBundle\Type\DataTypeEnum;

class MyMapper implements MapperInterface
{
    public static function getName(): string
    {
        return 'myMapper';
    }

    public function getMapping(): MappingInterface
    {
        return (new Mapping())
            ->add(name: 'id', type: DataTypeEnum::STRING);
    }

    public function getData(): \Generator
    {
        yield [];
    }

    public function getDataCount(): ?int
    {
        return 0;
    }
}
