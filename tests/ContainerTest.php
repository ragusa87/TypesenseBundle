<?php

namespace Biblioteca\TypesenseBundle\Tests;

use Biblioteca\TypesenseBundle\Mapper\Locator\MapperLocator;
use Biblioteca\TypesenseBundle\Tests\Client\ServiceWithClient;
use Typesense\Aliases;
use Typesense\Collection;
use Typesense\Debug;

class ContainerTest extends KernelTestCase
{
    public function testMapperLocatorExists(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        $this->assertContainerHas($container, MapperLocator::class);
    }

    public function testClientFactory(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        $this->assertContainerHas($container, ServiceWithClient::class);

        $service = $container->get(ServiceWithClient::class);
        $this->assertInstanceOf(ServiceWithClient::class, $service);

        $client = $service->getClient();
        $this->assertInstanceOf(Collection::class, $client->getCollection('books'));
        $this->assertInstanceOf(Collection::class, $client->getCollections()['books']);
        $this->assertInstanceOf(Debug::class, $client->getDebug());
        $this->assertInstanceOf(Aliases::class, $client->getAliases());
    }

    public function testClientFactoryInvalidUrl(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $kernel = self::bootKernel([
            'configs' => [self::CONFIG_KEY => __DIR__.'/config/packages/biblioteca_typesense_wrong_url.yaml'],
        ]);
        $container = $kernel->getContainer();

        $this->assertContainerHas($container, ServiceWithClient::class);

        $service = $container->get(ServiceWithClient::class);
        $this->assertInstanceOf(ServiceWithClient::class, $service);
    }
}
