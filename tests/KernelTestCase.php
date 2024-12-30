<?php

namespace Biblioteca\TypesenseBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as BaseKernelTestCase;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class KernelTestCase extends BaseKernelTestCase
{
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

        $env = $options['environment'] ?? $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'test';
        $debug = $options['debug'] ?? $_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] ?? true;

        $kernel = new static::$class($env, $debug, $options);
        if (!$kernel instanceof KernelInterface) {
            throw new \InvalidArgumentException('Kernel must be an instance of '.KernelInterface::class);
        }

        return $kernel;
    }

    protected function assertContainerHas(string $serviceId): void
    {
        $this->assertTrue(self::getContainer()->has($serviceId), sprintf('The service "%s" should be in the container.', $serviceId));
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
        $service = static::getContainer()->get($id);
        if (!$service instanceof $id) {
            throw new \InvalidArgumentException(sprintf('The service "%s" should be in the container.', $id));
        }

        return $service;
    }
}
