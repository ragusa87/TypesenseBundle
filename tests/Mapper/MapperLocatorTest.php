<?php

namespace Biblioteca\TypesenseBundle\Tests\Mapper;

use Biblioteca\TypesenseBundle\Mapper\Locator\MapperLocator;
use Biblioteca\TypesenseBundle\Tests\KernelTestCase;

class MapperLocatorTest extends KernelTestCase
{
    public function testHas(): void
    {
        self::bootKernel();

        $locator = $this->get(MapperLocator::class);
        $this->assertTrue($locator->has('products'));
    }

    public function testGetMappers(): void
    {
        self::bootKernel();

        $locator = $this->get(MapperLocator::class);
        $mappers = iterator_to_array($locator->getMappers());

        $this->assertArrayHasKey('products', $mappers);
    }

    public function testCount(): void
    {
        self::bootKernel();

        $locator = $this->get(MapperLocator::class);
        $this->assertSame(1, $locator->count());
    }

    public function testGetUnknown(): void
    {
        self::bootKernel();

        $locator = $this->get(MapperLocator::class);
        try {
            $locator->get('unknown');
        } catch (\InvalidArgumentException $e) {
            $this->assertStringStartsWith('The mapping service "unknown" is not found, do you implement ', $e->getMessage());

            return;
        }
        $this->fail(
            'An \InvalidArgumentException has not been raised.'
        );
    }
}
