<?php

namespace Biblioteca\TypesenseBundle\Mapper\Converter;

use Biblioteca\TypesenseBundle\Mapper\Converter\Exception\ValueConversionException;

interface ValueConverterInterface
{
    /**
     * @throws ValueConversionException
     */
    public function convert(mixed $value, string $type, bool $optional = true): mixed;
}
