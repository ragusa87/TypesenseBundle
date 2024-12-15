<?php

namespace Biblioteca\TypesenseBundle\Mapper\Locator;

use Biblioteca\TypesenseBundle\Mapper\MapperInterface;
use Psr\Container\ContainerExceptionInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

readonly class MapperLocator implements MapperLocatorInterface
{
    /**
     * @param ServiceLocator<mixed> $serviceLocator
     */
    public function __construct(private ServiceLocator $serviceLocator)
    {
    }

    public function has(string $name): bool
    {
        return $this->serviceLocator->has($name);
    }

    /**
     * @throws InvalidTypeMapperException
     */
    public function get(string $name): MapperInterface
    {
        try {
            $service = $this->serviceLocator->get($name);
        } catch (ContainerExceptionInterface $e) {
            throw new \InvalidArgumentException(sprintf('The service "%s" is not a valid mapper.', $name), 0, $e);
        }

        if (!$service instanceof MapperInterface) {
            throw new InvalidTypeMapperException(sprintf('The mapper "%s" must implement "%s".', $name, MapperInterface::class));
        }

        return $service;
    }

    /**
     * @return \Generator<string, MapperInterface>
     */
    public function getMappers(): \Generator
    {
        $mappers = [];
        foreach (array_keys($this->serviceLocator->getProvidedServices()) as $name) {
            try {
                $service = $this->serviceLocator->get($name);
                if (!$service instanceof MapperInterface) {
                    throw new InvalidTypeMapperException(sprintf('The mapper "%s" must implement "%s".', $name, MapperInterface::class));
                }
                $mappers[$name] = $service;
            } catch (ContainerExceptionInterface) {
                continue;
            }
        }
        yield from $mappers;
    }

    public function count(): int
    {
        return count($this->serviceLocator);
    }
}
