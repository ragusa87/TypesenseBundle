<?php

namespace Biblioteca\TypesenseBundle\Mapper\Converter\Exception;

class ValueExtractorException extends \Exception
{
    public function __construct(mixed $value, string $path, ?\Throwable $throwable = null)
    {
        $valueType = get_debug_type($value);
        parent::__construct(sprintf('Can not extract %s from %s', $path, $valueType), 0, $throwable);
    }
}
