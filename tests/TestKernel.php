<?php

namespace Biblioteca\TypesenseBundle\Tests;

use Biblioteca\TypesenseBundle\BibliotecaTypesenseBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    use MicroKernelTrait;
    public const CONFIG_KEY = 'biblioteca_typesense';

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

    public function boot(): void
    {
        if (!$this->booted) {
            $this->clearCache();
        }
        parent::boot();
    }

    /**
     * @ihneritDoc
     */
    public function registerBundles(): iterable
    {
        $bundles = array_merge([
            DoctrineBundle::class,
            BibliotecaTypesenseBundle::class,
            FrameworkBundle::class,
            DoctrineFixturesBundle::class,
        ], $this->settings['bundles'] ?? []);

        foreach ($bundles as $bundle) {
            $instance = new $bundle();

            if (!$instance instanceof BundleInterface) {
                throw new \InvalidArgumentException(sprintf('Bundle %s must be an instance of %s', $instance::class, BundleInterface::class));
            }
            yield $instance;
        }
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $this->settings['configs'][] = 'config/packages/doctrine.yaml';
        $this->settings['configs'][] = 'config/packages/framework.yaml';
        $this->settings['configs'][] = 'config/services.yaml';
        if (false === in_array(self::CONFIG_KEY, array_keys($this->settings['configs']))) {
            $this->settings['configs'][self::CONFIG_KEY] = 'config/packages/biblioteca_typesense.yaml';
        }
        foreach ($this->settings['configs'] as $config) {
            $loader->load(__DIR__.'/'.$config);
        }
    }

    public function getCacheDir(): string
    {
        return $this->getProjectDir().'/cache';
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir().'/logs';
    }

    public function getProjectDir(): string
    {
        return __DIR__.'/kernel';
    }

    public function shutdown(): void
    {
        parent::shutdown();

        $this->clearCache();
    }

    private function clearCache(): void
    {
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
