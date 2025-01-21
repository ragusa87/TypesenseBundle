<?php

namespace Biblioverse\TypesenseBundle\Tests\Mapper\Entity;

use Biblioverse\TypesenseBundle\Mapper\Entity\EntityMapperGenerator;
use Biblioverse\TypesenseBundle\Type\DataTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EntityMapperGenerator::class)]
class EntityMapperGeneratorTest extends TestCase
{
    private const DEFAULT_MAPPING = [
        'metadata' => [],
        'token_separators' => [],
        'default_sorting_field' => null,
        'symbols_to_index' => [],
        'fields' => [],
    ];

    public function testMapping(): void
    {
        $entityMapperGenerator = new EntityMapperGenerator([
            'fields' => [
                [
                    'name' => 'id',
                    'type' => 'string',
                ],
                [
                    'name' => 'name',
                    'type' => DataTypeEnum::BOOL->value,
                ],
            ],
        ] + self::DEFAULT_MAPPING);
        $mapping = $entityMapperGenerator->getMapping();
        $this->assertCount(2, $mapping->getFields());
        $this->assertNull($mapping->getCollectionOptions());
        $this->assertNull($mapping->getMetadata());

        $first = $mapping->getFields()[0];
        $this->assertSame('id', $first->getName());
        $this->assertSame('string', $first->getType());
        $this->assertNull($first->getEntityAttribute());

        $second = $mapping->getFields()[1];
        $this->assertSame('name', $second->getName());
        $this->assertSame(DataTypeEnum::BOOL->value, $second->getType());
        $this->assertNull($second->getEntityAttribute());
    }

    public function testMetadata(): void
    {
        $entityMapperGenerator = new EntityMapperGenerator([
            'metadata' => [
                'indexed_at' => 'today',
            ],
        ] + self::DEFAULT_MAPPING);

        $mapping = $entityMapperGenerator->getMapping();

        $metadata = $mapping->getMetadata();
        $this->assertNotNull($metadata);
        $this->assertSame(['indexed_at' => 'today'], $metadata->toArray());
    }

    public function testDefaultSorting(): void
    {
        $entityMapperGenerator = new EntityMapperGenerator([
            'default_sorting_field' => 'id',
        ] + self::DEFAULT_MAPPING);

        $mapping = $entityMapperGenerator->getMapping();

        $options = $mapping->getCollectionOptions();
        $this->assertNotNull($options);
        $this->assertSame(['default_sorting_field' => 'id'], $options->toArray());
    }

    public function testInvalidIdType(): void
    {
        $entityMapperGenerator = new EntityMapperGenerator([
            'fields' => [[
                'name' => 'id',
                'type' => 'int',
            ]]] + self::DEFAULT_MAPPING);

        try {
            $entityMapperGenerator->getMapping();
            $this->fail('An exception should have been thrown');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('The id field must be of type string', $e->getMessage());
        }
    }

    public function testAddId(): void
    {
        $entityMapperGenerator = new EntityMapperGenerator([
            'fields' => [
                [
                    'name' => 'name',
                    'type' => DataTypeEnum::BOOL->value,
                ],
            ],
        ] + self::DEFAULT_MAPPING);
        $mapping = $entityMapperGenerator->getMapping();
        $this->assertCount(2, $mapping->getFields());

        $first = $mapping->getFields()[0];
        $this->assertSame('id', $first->getName());
        $this->assertSame('string', $first->getType());
    }
}
