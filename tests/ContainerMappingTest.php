<?php

use Biblioverse\TypesenseBundle\Mapper\Fields\FieldMappingInterface;
use Biblioverse\TypesenseBundle\Mapper\Locator\MapperLocator;
use Biblioverse\TypesenseBundle\Tests\Entity\Product;
use Biblioverse\TypesenseBundle\Tests\KernelTestCase;
use Biblioverse\TypesenseBundle\Tests\TestKernel;

class ContainerMappingTest extends KernelTestCase
{
    public function testWithMapping(): void
    {
        self::bootKernel([
            'configs' => [TestKernel::CONFIG_KEY => 'config/packages/biblioverse_typesense_mapping.yaml'],
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
                'optional' => false,
                'type' => 'string',
            ],
            [
                'facet' => true,
                'locale' => 'en',
                'name' => 'name',
                'sort' => true,
                'type' => 'string',
            ],
        ], $fields);
    }
}
