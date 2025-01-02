<?php

use Biblioteca\TypesenseBundle\Mapper\Fields\FieldMappingInterface;
use Biblioteca\TypesenseBundle\Mapper\Locator\MapperLocator;
use Biblioteca\TypesenseBundle\Tests\Entity\Product;
use Biblioteca\TypesenseBundle\Tests\KernelTestCase;
use Biblioteca\TypesenseBundle\Tests\TestKernel;

class ContainerMappingTest extends KernelTestCase
{
    public function testWithMapping(): void
    {
        self::bootKernel([
            'configs' => [TestKernel::CONFIG_KEY => __DIR__.'/config/packages/biblioteca_typesense_mapping.yaml'],
        ]);

        $mapperLocator = $this->get(MapperLocator::class);
        $services = $mapperLocator->getEntityMappers(Product::class);
        $this->assertCount(1, $services, 'There should be one service for the entity Product');
        $this->assertArrayHasKey('products', $services, 'The key must match the index name');

        $mapping = $services['products']->getMapping();

        // Test metadata
        $metadata = $mapping->getMetadata();
        $this->assertSame([
            'primary_key' => 'id',
        ], $metadata?->toArray());

        // Test collection options
        $options = $mapping->getCollectionOptions();
        $this->assertSame([
            'token_separators' => [' ', '-'],
            'symbols_to_index' => ['&'],
            'default_sorting_field' => 'name',
        ], $options?->toArray());

        // Test fields
        $this->assertCount(2, $mapping->getFields());
        $fields = array_map(fn (FieldMappingInterface $fieldMapping) => $fieldMapping->toArray(), $mapping->getFields());
        $this->assertSame([
            [
                'name' => 'id',
                'type' => 'string',
            ],
            [
                'name' => 'name',
                'type' => 'string',
            ],
        ], $fields);
    }
}
