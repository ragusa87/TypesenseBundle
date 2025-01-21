<?php

namespace Biblioverse\TypesenseBundle\Tests\Mapper\Fields;

use Biblioverse\TypesenseBundle\Mapper\Fields\FieldMapping;
use Biblioverse\TypesenseBundle\Type\DataTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FieldMapping::class)]
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
