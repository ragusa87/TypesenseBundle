<?php

namespace Biblioteca\TypesenseBundle\Tests\Mapper\Fields;

use Biblioteca\TypesenseBundle\Mapper\Fields\FieldMapping;
use Biblioteca\TypesenseBundle\Type\DataTypeEnum;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Biblioteca\TypesenseBundle\Mapper\Fields\FieldMapping
 */
class FieldMappingTest extends TestCase
{
    public function testFieldMapping(): void
    {
        $fieldMapping = new FieldMapping(
            name: 'name',
            type: DataTypeEnum::STRING,
            facet: true,
            optional: false
        );

        $this->assertCount(4, $fieldMapping->toArray(), 'FieldMapping::toArray has some missing fields');
        $this->assertSame('string', $fieldMapping->getType());
        $this->assertSame('name', $fieldMapping->getName());
    }
}
