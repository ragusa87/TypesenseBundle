<?php

namespace Biblioverse\TypesenseBundle\Mapper;

use Biblioverse\TypesenseBundle\Mapper\Mapping\MappingInterface;

interface MappingGeneratorInterface
{
    public function getMapping(): MappingInterface;
}
