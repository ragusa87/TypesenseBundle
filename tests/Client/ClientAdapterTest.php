<?php

namespace Biblioteca\TypesenseBundle\Tests\Client;

use Biblioteca\TypesenseBundle\Client\ClientInterface;
use Biblioteca\TypesenseBundle\Tests\KernelTestCase;
use Typesense\Aliases;
use Typesense\Analytics;
use Typesense\Collection;
use Typesense\Debug;
use Typesense\Health;
use Typesense\Keys;
use Typesense\Metrics;
use Typesense\MultiSearch;
use Typesense\Operations;
use Typesense\Presets;

class ClientAdapterTest extends KernelTestCase
{
    public function testClient(): void
    {
        $client = $this->get(ClientInterface::class);
        $this->assertInstanceOf(Collection::class, $client->getCollection('books'));
        $this->assertInstanceOf(Collection::class, $client->getCollections()['books']);
        $this->assertInstanceOf(Debug::class, $client->getDebug());
        $this->assertInstanceOf(Aliases::class, $client->getAliases());
        $this->assertInstanceOf(Keys::class, $client->getKeys());
        $this->assertInstanceOf(Metrics::class, $client->getMetrics());
        $this->assertInstanceOf(Health::class, $client->getHealth());
        $this->assertInstanceOf(Operations::class, $client->getOperations());
        $this->assertInstanceOf(MultiSearch::class, $client->getMultiSearch());
        $this->assertInstanceOf(Presets::class, $client->getPresets());
        $this->assertInstanceOf(Analytics::class, $client->getAnalytics());
    }
}
