<?php

namespace Biblioverse\TypesenseBundle\Tests\Mapper\Locator;

use Biblioverse;
use Biblioverse\TypesenseBundle\Mapper\DataGeneratorInterface;
use Biblioverse\TypesenseBundle\Mapper\Locator\InvalidTypeMapperException;
use Biblioverse\TypesenseBundle\Mapper\Locator\MapperLocator;
use Biblioverse\TypesenseBundle\Mapper\MappingGeneratorInterface;
use Biblioverse\TypesenseBundle\Tests\Entity\Product;
use Biblioverse\TypesenseBundle\Tests\TestKernel;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MapperLocator::class)]
class MapperLocatorTest extends Biblioverse\TypesenseBundle\Tests\KernelTestCase
{
    public function testLocatorMyMapper(): void
    {
        static::ensureKernelShutdown();
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        $locator = $container->get(MapperLocator::class);
        $this->assertInstanceOf(MapperLocator::class, $locator);

        $this->assertSame(1, $locator->countDataGenerator(), 'The locator should have exactly 1 data generator.');
        $this->assertTrue($locator->hasDataGenerator('products'), 'The locator should have the products service.');
        $this->assertInstanceOf(DataGeneratorInterface::class, $locator->getDataGenerator('products'), 'The locator should return an instance of DataGeneratorInterface.');
    }

    public function testLocatorUnknownService(): void
    {
        self::bootKernel();
        $mapperLocator = $this->get(MapperLocator::class);

        $this->expectException(\InvalidArgumentException::class);
        $mapperLocator->getDataGenerator('unknown');
    }

    public function testLocatorInstanceOfIssue(): void
    {
        static::ensureKernelShutdown();
        self::bootKernel([
            'configs' => ['config/services_with_wrong_mapper.yaml'],
        ]);

        $mapperLocator = $this->get(MapperLocator::class);
        try {
            $mapperLocator->getDataGenerator('myInvalidMapper');
            $this->fail('The locator should throw an exception.');
        } catch (InvalidTypeMapperException $e) {
            $this->assertStringContainsString('No data generator found', $e->getMessage());
        }
    }

    public function testLocatorGetMappersInstanceOfIssue(): void
    {
        self::bootKernel([
            'configs' => ['config/services_with_wrong_mapper.yaml'],
        ]);

        $mapperLocator = $this->get(MapperLocator::class);

        try {
            $this->assertGreaterThan(42, count($mapperLocator->getMappers()), 'The locator have thrown an exception.');
            $this->fail('The locator should throw an exception.');
        } catch (InvalidTypeMapperException $e) {
            $this->assertStringContainsString('not found', $e->getMessage());
        }
    }

    public function testLocatorGetMappers(): void
    {
        self::bootKernel();

        $mapperLocator = $this->get(MapperLocator::class);
        $result = $mapperLocator->getMappers();

        $this->assertArrayHasKey('products', $result);
        $this->assertInstanceOf(MappingGeneratorInterface::class, $result['products']);
    }

    public function testLocatorGetMappingGenerator(): void
    {
        self::bootKernel([
            'configs' => [TestKernel::CONFIG_KEY => 'config/packages/biblioverse_typesense_mapping.yaml'],
        ]);

        $mapperLocator = $this->get(MapperLocator::class);
        $result = $mapperLocator->getMappers();

        $this->assertArrayHasKey('products', $result);
        $this->assertInstanceOf(MappingGeneratorInterface::class, $result['products']);
        $this->assertSame(1, $mapperLocator->countDataGenerator());
        $this->assertInstanceOf(DataGeneratorInterface::class, $mapperLocator->getDataGenerator('products'));

        $entityMappers = $mapperLocator->getEntityMappers(Product::class);
        $this->assertCount(1, $entityMappers);
        $this->assertArrayHasKey('products', $entityMappers);
        $this->assertInstanceOf(MappingGeneratorInterface::class, $entityMappers['products']);
    }

    public function testNoEntityTransformer(): void
    {
        self::bootKernel([
            'configs' => [TestKernel::CONFIG_KEY => 'config/packages/biblioverse_typesense_mapping.yaml'],
        ]);

        $mapperLocator = $this->get(MapperLocator::class);

        $this->assertFalse($mapperLocator->hasEntityTransformer(\stdClass::class));
    }
}
