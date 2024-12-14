<?php

namespace Biblioteca\TypesenseBundle\Tests;

use Biblioteca\TypesenseBundle\BibliotecaTypesenseBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    use MicroKernelTrait;

    /**
     * @param array{'configs'?: array{'biblioteca_typesense'?:string}, 'bundles'?:string[]} $settings
     */
    public function __construct(
        string $environment,
        bool $debug,
        private array $settings = [],
    ) {
        parent::__construct($environment, $debug);
    }

    /**
     * @ihneritDoc
     */
    public function registerBundles(): iterable
    {
        $bundles = array_merge([
            DoctrineBundle::class,
            BibliotecaTypesenseBundle::class,
        ], $this->settings['bundles'] ?? []);

        foreach ($bundles as $bundleClass) {
            $instance = new $bundleClass();

            if (!$instance instanceof BundleInterface) {
                throw new \InvalidArgumentException(sprintf('Bundle %s must be an instance of %s', get_class($instance), BundleInterface::class));
            }
            yield $instance;
        }
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $this->settings['configs'][] = __DIR__.'/config/packages/doctrine.yaml';
        $this->settings['configs'][] = __DIR__.'/config/services.yaml';

        foreach ($this->settings['configs'] as $config) {
            $loader->load($config);
        }
    }

    public function getCacheDir(): string
    {
        return realpath(sys_get_temp_dir()).'/TypesenseTests/cache';
    }

    public function getLogDir(): string
    {
        return realpath(sys_get_temp_dir()).'/TypesenseTests/log';
    }

    public function getProjectDir(): string
    {
        return __DIR__.'/kernel';
    }

    public function shutdown(): void
    {
        parent::shutdown();

        $cacheDirectory = $this->getCacheDir();
        $logDirectory = $this->getLogDir();

        $filesystem = new Filesystem();

        if ($filesystem->exists($cacheDirectory)) {
            $filesystem->remove($cacheDirectory);
        }

        if ($filesystem->exists($logDirectory)) {
            $filesystem->remove($logDirectory);
        }
    }
}
