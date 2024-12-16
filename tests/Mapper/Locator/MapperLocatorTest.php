<?php

namespace Biblioteca\TypesenseBundle\Tests\Mapper\Locator;

use Biblioteca;
use Biblioteca\TypesenseBundle\Mapper\Fields\FieldMapping;
use Biblioteca\TypesenseBundle\Mapper\Locator\InvalidTypeMapperException;
use Biblioteca\TypesenseBundle\Mapper\Locator\MapperLocator;
use Biblioteca\TypesenseBundle\Tests\Mapper\ProductMapper;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FieldMapping::class)]
class MapperLocatorTest extends Biblioteca\TypesenseBundle\Tests\KernelTestCase
{
    public function testLocatorMyMapper(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        $locator = $container->get(MapperLocator::class);
        $this->assertInstanceOf(MapperLocator::class, $locator);

        $this->assertSame(1, $locator->count(), 'The locator should have 1 service.');
        $this->assertTrue($locator->has('products'), 'The locator should have the products service.');
        $this->assertInstanceOf(ProductMapper::class, $locator->get('products'), 'The locator should return an instance of ProductMapper.');
    }

    public function testLocatorUnknownService(): void
    {
        self::bootKernel();
        $locator = $this->get(MapperLocator::class);

        $this->expectException(\InvalidArgumentException::class);
        $locator->get('unknown');
    }

    public function testLocatorInstanceOfIssue(): void
    {
        $this->expectException(InvalidTypeMapperException::class);
        self::bootKernel([
            'configs' => [dirname(__DIR__, 2).'/config/services_with_wrong_mapper.yaml'],
        ]);

        $locator = $this->get(MapperLocator::class);
        $locator->get('myInvalidMapper');
    }

    public function testLocatorGetMappersInstanceOfIssue(): void
    {
        $this->expectException(InvalidTypeMapperException::class);
        self::bootKernel([
            'configs' => [dirname(__DIR__, 2).'/config/services_with_wrong_mapper.yaml'],
        ]);

        $locator = $this->get(MapperLocator::class);
        iterator_to_array($locator->getMappers());
    }

    public function testLocatorGetMappers(): void
    {
        self::bootKernel();

        $locator = $this->get(MapperLocator::class);
        $result = iterator_to_array($locator->getMappers());

        $this->assertArrayHasKey('products', $result);
        $this->assertInstanceOf(ProductMapper::class, $result['products']);
    }
}
