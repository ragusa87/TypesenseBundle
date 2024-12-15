<?php

namespace Biblioteca\TypesenseBundle\Tests\Mapper\Locator;

use Biblioteca;
use Biblioteca\TypesenseBundle\Mapper\Locator\InvalidTypeMapperException;
use Biblioteca\TypesenseBundle\Mapper\Locator\MapperLocator;
use Biblioteca\TypesenseBundle\Tests\Mapper\MyMapper;

/**
 * @covers \Biblioteca\TypesenseBundle\Mapper\Locator\MapperLocator
 */
class MapperLocatorTest extends Biblioteca\TypesenseBundle\Tests\KernelTestCase
{
    public function testLocatorMyMapper(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        $locator = $container->get(MapperLocator::class);
        $this->assertInstanceOf(MapperLocator::class, $locator);

        $this->assertSame(1, $locator->count(), 'The locator should have 1 service.');
        $this->assertTrue($locator->has('myMapper'), 'The locator should have the myMapper service.');
        $this->assertInstanceOf(MyMapper::class, $locator->get('myMapper'), 'The locator should return an instance of MyMapper.');
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

        $this->assertArrayHasKey('myMapper', $result);
        $this->assertInstanceOf(MyMapper::class, $result['myMapper']);
    }
}
