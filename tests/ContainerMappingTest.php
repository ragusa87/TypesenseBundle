<?php

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
    }
}
