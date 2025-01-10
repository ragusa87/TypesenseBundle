<?php

namespace Biblioteca\TypesenseBundle;

use Biblioteca\TypesenseBundle\Mapper\DataGeneratorInterface;
use Biblioteca\TypesenseBundle\Mapper\Entity\EntityMapperGenerator;
use Biblioteca\TypesenseBundle\Mapper\Entity\EntityTransformerInterface;
use Biblioteca\TypesenseBundle\Mapper\MappingGeneratorInterface;
use Biblioteca\TypesenseBundle\Mapper\StandaloneCollectionManagerInterface;
use Biblioteca\TypesenseBundle\Search\SearchCollectionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @phpstan-import-type MappingConfiguration from EntityMapperGenerator
 */
class BibliotecaTypesenseBundle extends AbstractBibliotecaTypesenseBundle
{
    public const DATA_GENERATOR_TAG_NAME = 'biblioteca_typesense.entity_data_generator';
    public const DATA_MAPPER_GENERATOR_TAG_NAME = 'biblioteca_typesense.entity_mapper_generator';
    public const ENTITY_TRANSFORMER_TAG_NAME = 'biblioteca_typesense.entity_transformer';

    /**
     * @param array{typesense: array{uri: string, key: string, connection_timeout_seconds: int}, collections: array<string, array{entity: string, name?: string}>} $config
     */
    public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->registerForAutoconfiguration(StandaloneCollectionManagerInterface::class)
            ->addTag(StandaloneCollectionManagerInterface::TAG_NAME);

        /** @var iterable<string,mixed> $typesenseConfig */
        $typesenseConfig = $config['typesense'];
        foreach ($typesenseConfig as $key => $value) {
            $containerConfigurator->parameters()->set('biblioteca_typesense.config.'.$key, $value);
        }

        $containerConfigurator->import(__DIR__.'/Resources/config/services.yaml');

        $this->loadCollection($config['collections'], $containerConfigurator);
    }

    /**
     * @param array<string, array{entity: string, name?: string, mapping?: MappingConfiguration}> $collections
     */
    public function loadCollection(array $collections, ContainerConfigurator $containerConfigurator): void
    {
        $entityMapping = [];
        $entityMapperGenerator = [];
        $entityDataGenerator = [];
        $entityTransformer = [];

        foreach ($collections as $name => $collection) {
            // Map the entity to the collection name
            $entityName = $collection['entity'];
            $entityMapping[$entityName][] = $name;

            $this->createSearchService($containerConfigurator, $entityName, $name);

            // Create mapper
            if (isset($collection['mapping']['fields'])) {
                /* @var MappingConfiguration $mapping */
                $mapping = $collection['mapping'];
                $mapperGeneratorId = $this->createMapperGenerator($containerConfigurator, $name, $mapping);
                $entityMapperGenerator[$name] = new Reference($mapperGeneratorId);

                // Create entity transformer
                $entityTransformerId = $this->createEntityTransformer($containerConfigurator, $name, $mapperGeneratorId);
                $entityTransformer[$name] = $entityTransformerId;

                // Create data generator
                $id = $this->createDataGenerator($containerConfigurator, $entityName, $name, $entityTransformerId);
                $entityDataGenerator[$name] = new Reference($id);
            }
        }

        // Declare the configured mapping as parameters
        $containerConfigurator->parameters()->set('biblioteca_typesense.config.entity_mapping', $entityMapping);
    }

    private function toCamelCase(string $input): string
    {
        // Replace non-alphanumeric characters with a space
        $input = preg_replace('/[^a-zA-Z0-9]+/', ' ', $input);

        // Capitalize the first letter of each word and remove spaces
        $camelCased = str_replace(' ', '', ucwords((string) $input));

        // Ensure the first letter is lowercase
        return lcfirst($camelCased);
    }

    private function createSearchService(ContainerConfigurator $containerConfigurator, string $entity, string $name): void
    {
        // Create a service that will be used to search the specific collection
        $id = 'biblioteca_typesense.collection.'.$name;
        $containerConfigurator->services()
            ->set($id)
            ->parent('biblioteca_typesense.collection.abstract')
            ->bind('$collectionName', $name)
            ->bind('$entityClass', $entity)
            ->public()
            ->autowire();

        // You can inject SearchCollectionInterface in your service
        // with the name "SearchBooks", given the collection name is "books".
        $this->addAlias($containerConfigurator, SearchCollectionInterface::class, 'Search '.$name, $id);
    }

    /**
     * @param MappingConfiguration $mapping
     *
     * @return string Service id
     */
    private function createMapperGenerator(ContainerConfigurator $containerConfigurator, string $name, array $mapping): string
    {
        // If the configuration has a mapping, create a service to do the mapping automatically
        $id = 'biblioteca_typesense.entity_mapper_generator.'.$name;
        $containerConfigurator->services()
            ->set($id)
            ->parent('biblioteca_typesense.entity_mapper_generator.abstract')
            ->bind('$mappingConfig', $mapping)
            // The mapper attribute is used as index and reference the collection.
            ->tag(self::DATA_MAPPER_GENERATOR_TAG_NAME, ['key' => $name])
            ->private()
            ->autowire();

        // You can inject MappingGeneratorInterface with the collection name
        $this->addAlias($containerConfigurator, MappingGeneratorInterface::class, 'mappingGenerator '.$name, $id);

        return $id;
    }

    private function createDataGenerator(ContainerConfigurator $containerConfigurator, string $entity, string $name, string $entityTransformerId): string
    {
        $id = 'biblioteca_typesense.entity_data_generator.'.$name;
        $containerConfigurator->services()
            ->set($id)
            ->parent('biblioteca_typesense.entity_data_generator.abstract')
            ->bind('$className', $entity)
            ->bind('$entityTransformer', new Reference($entityTransformerId))
            ->tag(self::DATA_GENERATOR_TAG_NAME, ['key' => $name])
            ->private()
            ->autowire();

        $this->addAlias($containerConfigurator, DataGeneratorInterface::class, 'dataGenerator '.$name, $id);

        return $id;
    }

    private function createEntityTransformer(ContainerConfigurator $containerConfigurator, string $name, string $mappingGeneratorId): string
    {
        $id = 'biblioteca_typesense.entity_transformer.'.$name;
        $containerConfigurator->services()
            ->set($id)
            ->parent('biblioteca_typesense.entity_transformer.abstract')
            ->bind('$mappingGenerator', new Reference($mappingGeneratorId))
            ->tag(self::ENTITY_TRANSFORMER_TAG_NAME, ['key' => $name])
            ->private()
            ->autowire();

        // You can inject EntityTransformerInterface with the collection name
        $this->addAlias($containerConfigurator, EntityTransformerInterface::class, 'entityTransformer '.$name, $id);

        return $id;
    }

    private function addAlias(ContainerConfigurator $containerConfigurator, string $class, string $nameToCamelCase, string $id): void
    {
        // You can inject MappingGeneratorInterface with the collection name
        $bindingName = '$'.$this->toCamelCase($nameToCamelCase);
        $containerConfigurator->services()->defaults()
            ->alias($class.' '.$bindingName, new Reference($id));
    }
}
