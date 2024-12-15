<?php

namespace Biblioteca\TypesenseBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as BaseKernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class KernelTestCase extends BaseKernelTestCase
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

    protected function assertContainerHas(ContainerInterface $container, string $serviceId): void
    {
        $this->assertTrue($container->has($serviceId), sprintf('The service "%s" should be in the container.', $serviceId));
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $id
     *
     * @return object&T
     */
    public function get(string $id): object
    {
        assert(self::$kernel !== null);
        $service = self::$kernel->getContainer()->get($id);
        if (!$service instanceof $id) {
            throw new \InvalidArgumentException(sprintf('The service "%s" should be in the container.', $id));
        }

        return $service;
    }
}
