<?php

namespace Biblioteca\TypesenseBundle\Mapper;

use Biblioteca\TypesenseBundle\Mapper\Mapping\MappingInterface;

interface MappingGeneratorInterface
{
    public function getMapping(): MappingInterface;
}
