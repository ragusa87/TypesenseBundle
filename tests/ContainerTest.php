<?php

namespace Biblioteca\TypesenseBundle\Tests;

use Biblioteca\TypesenseBundle\Mapper\Locator\MapperLocator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class ContainerTest extends KernelTestCase
{
    public const CONFIG_KEY = 'biblioteca_typesense';

    /**
     * @return class-string<KernelInterface>
     */
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    /**
     * @param array{'bundles'?: class-string<BundleInterface>, 'configs'?: array<string|int,string>, 'environment'?: string, 'debug'?:bool} $options
     */
    protected static function createKernel(array $options = []): KernelInterface
    {
        static::$class ??= static::getKernelClass();

        if (false === in_array(self::CONFIG_KEY, array_keys($options['configs'] ?? []))) {
            $options['configs'][self::CONFIG_KEY] = __DIR__.'/config/packages/biblioteca_typesense.yaml';
        }
        $env = $options['environment'] ?? $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'test';
        $debug = $options['debug'] ?? $_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] ?? true;

        $kernel = new static::$class($env, $debug, $options);
        if (!$kernel instanceof KernelInterface) {
            throw new \InvalidArgumentException('Kernel must be an instance of '.KernelInterface::class);
        }

        return $kernel;
    }

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

    protected function assertContainerHas(ContainerInterface $container, string $serviceId): void
    {
        $this->assertTrue($container->has($serviceId), sprintf('The service "%s" should be in the container.', $serviceId));
    }
}
