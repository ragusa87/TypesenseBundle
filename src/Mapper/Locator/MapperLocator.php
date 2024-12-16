<?php

namespace Biblioteca\TypesenseBundle\Mapper\Locator;

use Biblioteca\TypesenseBundle\Mapper\Entity\EntityMapperInterface;
use Biblioteca\TypesenseBundle\Mapper\MapperInterface;
use Biblioteca\TypesenseBundle\Mapper\StandaloneMapperInterface;
use Psr\Container\ContainerExceptionInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

class MapperLocator implements MapperLocatorInterface
{
    /**
     * @param ServiceLocator<mixed>   $serviceLocator
     * @param array<string, string[]> $entityMapping  index is the entity class name, values are the mapper names for this entity
     */
    public function __construct(
        private readonly ServiceLocator $serviceLocator,
        private readonly array $entityMapping,
    ) {
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
            throw new \InvalidArgumentException(sprintf('The mapping service "%s" is not found, do you implement "%s" ?', $name, StandaloneMapperInterface::class), 0, $e);
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

    /**
     * @template T of object
     *
     * @param class-string<T> $classString
     *
     * @return array<string, EntityMapperInterface<T>> indexed by the mapper name
     */
    public function getEntityMappers(string $classString): array
    {
        $response = [];
        foreach (($this->entityMapping[$classString] ?? []) as $mapperName) {
            $service = $this->get($mapperName);
            if (!$service instanceof EntityMapperInterface) {
                throw new InvalidTypeMapperException(sprintf('The mapper "%s" must implement "%s".', $mapperName, EntityMapperInterface::class));
            }

            $response[$mapperName] = $service;
        }

        return $response;
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $classString $classString
     */
    public function hasEntityMappers(string $classString): bool
    {
        return isset($this->entityMapping[$classString]);
    }
}
