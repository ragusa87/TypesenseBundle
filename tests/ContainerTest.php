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
        $kernel->getContainer();

        $this->assertContainerHas(MapperLocator::class);
    }

    public function testClientFactory(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->assertContainerHas(ServiceWithClient::class);

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
        self::bootKernel([
            'configs' => [TestKernel::CONFIG_KEY => __DIR__.'/config/packages/biblioteca_typesense_wrong_url.yaml'],
        ]);
        $container = self::getContainer();

        $this->assertContainerHas(ServiceWithClient::class);

        $service = $container->get(ServiceWithClient::class);
        $this->assertInstanceOf(ServiceWithClient::class, $service);

        try {
            // https://github.com/symfony/symfony/issues/53812 => can't use expectException yet.
            $service->getClient()->getCollection('books');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Invalid URI ./s?a=12&b=12.3.3.4:1233', $e->getMessage());

            return;
        }

        $this->fail('An \InvalidArgumentException has not been raised.');
    }
}
