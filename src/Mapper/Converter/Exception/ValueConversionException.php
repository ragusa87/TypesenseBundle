<?php

namespace Biblioteca\TypesenseBundle\Mapper\Converter\Exception;

class ValueConversionException extends \Exception
{
    public function __construct(mixed $value, string $type, ?\Throwable $throwable = null)
    {
        $valueType = get_debug_type($value);
        parent::__construct(sprintf('Can not convert %s to %s', $valueType, $type), 0, $throwable);
    }
}
