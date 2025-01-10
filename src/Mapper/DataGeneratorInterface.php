<?php

namespace Biblioteca\TypesenseBundle\Mapper;

use Biblioteca\TypesenseBundle\Mapper\Converter\Exception\ValueConversionException;
use Biblioteca\TypesenseBundle\Mapper\Converter\Exception\ValueExtractorException;

interface DataGeneratorInterface
{
    /**
     * Data to index, the key is the field name.
     *
     * @return \Generator<array<string, mixed>>
     *
     * @throws ValueConversionException
     * @throws ValueExtractorException
     */
    public function getData(): \Generator;

    /**
     * How many data to index. If null, the progression is unknown.
     */
    public function getDataCount(): ?int;
}
