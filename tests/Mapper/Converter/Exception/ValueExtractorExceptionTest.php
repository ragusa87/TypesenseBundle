<?php

namespace Biblioteca\TypesenseBundle\Tests\Mapper\Converter\Exception;

use Biblioteca\TypesenseBundle\Mapper\Converter\Exception\ValueExtractorException;

class ValueExtractorExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testException(): void
    {
        $this->expectException(ValueExtractorException::class);
        $this->expectExceptionMessage('Cannot extract string from int');

        throw new ValueExtractorException(12, 'string');
    }
}
