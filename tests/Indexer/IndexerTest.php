<?php

namespace Biblioteca\TypesenseBundle\Tests\Indexer;

use Biblioteca\TypesenseBundle\Indexer\Indexer;
use Biblioteca\TypesenseBundle\Indexer\IndexerInterface;
use Biblioteca\TypesenseBundle\Populate\PopulateService;
use Biblioteca\TypesenseBundle\Tests\Entity\Product;
use Biblioteca\TypesenseBundle\Tests\KernelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Indexer::class)]
class IndexerTest extends KernelTestCase
{
    public function testIndexEntity(): void
    {
        self::bootKernel();

        // Mock the PopulateService to assert 'fillData' is called once
        $populateMock = $this->createMock(PopulateService::class);
        $populateMock->expects($this->once())
            ->method('fillData')
            ->with('products', ['id' => 12, 'name' => 'test'])
            ->willReturnCallback(function () {
                yield ['id' => (string) 12, 'name' => 'test'];
            });

        static::getContainer()->set(PopulateService::class, $populateMock);

        $indexer = $this->get(IndexerInterface::class);

        $product = new Product();
        $product->id = 12;
        $product->name = 'test';

        $indexer->indexEntity($product);

        $this->assertTrue(true); // @phpstan-ignore-line Mock handle the test.
    }

    public function testRemoveEntity(): void
    {
        self::bootKernel();

        // Mock the PopulateService to assert 'fillData' is called once
        $populateMock = $this->createMock(PopulateService::class);
        $populateMock->expects($this->once())
            ->method('deleteData')
            ->with('products', ['id' => 12, 'name' => 'test'])
            ->willReturnCallback(function () {
                yield ['id' => (string) 12, 'name' => 'test'];
            });

        static::getContainer()->set(PopulateService::class, $populateMock);

        $indexer = $this->get(IndexerInterface::class);

        $product = new Product();
        $product->id = 12;
        $product->name = 'test';

        $indexer->removeEntity($product);

        $this->assertTrue(true); // @phpstan-ignore-line Mock handle the test.
    }
}
