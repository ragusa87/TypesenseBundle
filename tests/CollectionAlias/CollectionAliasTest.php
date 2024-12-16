<?php

namespace Biblioteca\TypesenseBundle\Tests\CollectionAlias;

use Biblioteca\TypesenseBundle\Client\ClientInterface;
use Biblioteca\TypesenseBundle\Client\ClientSingletonFactory;
use Biblioteca\TypesenseBundle\CollectionAlias\CollectionAlias;
use PHPUnit\Framework\Attributes\CoversClass;
use Typesense\Aliases;
use Typesense\Collection;

#[CoversClass(ClientSingletonFactory::class)]
class CollectionAliasTest extends \PHPUnit\Framework\TestCase
{
    public function testTemplate(): void
    {
        $client = $this->createMock(ClientInterface::class);

        $collectionAlias = new CollectionAlias($client, 'pre-%s-suffix');

        // Suffix works
        $this->assertStringStartsWith('pre-books-suffix-', $collectionAlias->getName('books'));

        // Date is injected
        $this->assertStringStartsWith('pre-books-suffix-'.date('Y'), $collectionAlias->getName('books'));
    }

    public function testSwitch(): void
    {
        $collection = $this->createMock(Collection::class);
        $aliases = $this->createMock(Aliases::class);

        $client = $this->getMockBuilder(ClientInterface::class)
            ->enableOriginalConstructor()
            ->getMock();
        $client->method('getCollection')
            ->willReturn($collection);

        $client->method('getAliases')
            ->willReturn($aliases);

        $aliases->expects($this->once())
            ->method('upsert')
            ->with('books', ['collection_name' => 'books_alias']);

        $collectionAlias = new CollectionAlias($client);
        $collectionAlias->switch('books', 'books_alias');

        $this->assertTrue(true); // @phpstan-ignore-line The mock will fail if the method is not called
    }
}
