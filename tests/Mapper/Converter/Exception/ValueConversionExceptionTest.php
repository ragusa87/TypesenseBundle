<?php

namespace Biblioteca\TypesenseBundle\Tests\Mapper\Converter\Exception;

use Biblioteca\TypesenseBundle\Mapper\Converter\Exception\ValueConversionException;

class ValueConversionExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testException(): void
    {
        $this->expectException(ValueConversionException::class);
        $this->expectExceptionMessage('Cannot convert int to string');

        throw new ValueConversionException(12, 'string');
    }
}
