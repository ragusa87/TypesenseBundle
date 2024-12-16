<?php

namespace Biblioteca\TypesenseBundle\Mapper\Converter;

use Biblioteca\TypesenseBundle\Mapper\Converter\Exception\ValueExtractorException;

interface ValueExtractorInterface
{
    /**
     * @throws ValueExtractorException
     */
    public function getValue(object $entity, string $name): mixed;
}
