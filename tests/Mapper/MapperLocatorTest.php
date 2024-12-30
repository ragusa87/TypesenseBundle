<?php

namespace Biblioteca\TypesenseBundle\Tests\Mapper;

use Biblioteca\TypesenseBundle\Mapper\Locator\MapperLocator;
use Biblioteca\TypesenseBundle\Tests\KernelTestCase;

class MapperLocatorTest extends KernelTestCase
{
    public function testHas(): void
    {
        self::bootKernel();

        $mapperLocator = $this->get(MapperLocator::class);
        $this->assertTrue($mapperLocator->has('products'));
    }

    public function testGetMappers(): void
    {
        self::bootKernel();

        $mapperLocator = $this->get(MapperLocator::class);
        $mappers = iterator_to_array($mapperLocator->getMappers());

        $this->assertArrayHasKey('products', $mappers);
    }

    public function testCount(): void
    {
        self::bootKernel();

        $mapperLocator = $this->get(MapperLocator::class);
        $this->assertSame(1, $mapperLocator->count());
    }

    public function testGetUnknown(): void
    {
        self::bootKernel();

        $mapperLocator = $this->get(MapperLocator::class);
        try {
            $mapperLocator->get('unknown');
        } catch (\InvalidArgumentException $e) {
            $this->assertStringStartsWith('The mapping service "unknown" is not found, do you implement ', $e->getMessage());

            return;
        }
        $this->fail(
            'An \InvalidArgumentException has not been raised.'
        );
    }
}
