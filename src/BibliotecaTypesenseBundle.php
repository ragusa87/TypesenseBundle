<?php

namespace Biblioteca\TypesenseBundle;

use Biblioteca\TypesenseBundle\Mapper\StandaloneMapperInterface;
use Biblioteca\TypesenseBundle\Search\SearchCollectionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

class BibliotecaTypesenseBundle extends AbstractBibliotecaTypesenseBundle
{
    /**
     * @param array{typesense: array{uri: string, key: string, connection_timeout_seconds: int}, collections: array<string, array{entity: string, name?: string}>} $config
     */
    public function loadExtension(array $config, ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->registerForAutoconfiguration(StandaloneMapperInterface::class)
            ->addTag(StandaloneMapperInterface::TAG_NAME);

        /** @var iterable<string,mixed> $typesenseConfig */
        $typesenseConfig = $config['typesense'];
        foreach ($typesenseConfig as $key => $value) {
            $containerConfigurator->parameters()->set('biblioteca_typesense.config.'.$key, $value);
        }

        $containerConfigurator->import(__DIR__.'/Resources/config/services.yaml');

        $this->loadCollection($config['collections'], $containerConfigurator, $containerBuilder);
    }

    /**
     * @param array<string, array{entity: string, name?: string, mapping?: array{'fields'?: array<string,mixed>}}> $collections
     */
    public function loadCollection(array $collections, ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void
    {
        $entityMapping = [];
        $entityServiceLocator = [];
        foreach ($collections as $name => $collection) {
            // Map the entity to the collection name
            $entityMapping[$collection['entity']][] = $name;

            // Create a service that will be used to search the specific collection
            $id = 'biblioteca_typesense.collection.'.$name;
            $containerConfigurator->services()
                ->set($id)
                ->parent('biblioteca_typesense.collection.abstract')
                ->bind('$collectionName', $name)
                ->bind('$entityClass', $collection['entity'])
                ->public()
                ->autowire();

            // You can inject ExecuteCollectionSearchResultInterface in your service
            // with the name "SearchBooks", given the collection name is "books".
            $bindingName = '$'.$this->toCamelCase('Search '.$name);
            $containerConfigurator->services()->defaults()
                ->alias(SearchCollectionInterface::class.' '.$bindingName, new Reference($id));

            if (($collection['mapping']['fields'] ?? []) === []) {
                continue;
            }

            // If the configuration has a mapping, create a service to do the mapping automatically
            $id = 'biblioteca_typesense.entity_mapper.'.$name;
            $containerConfigurator->services()
                ->set($id)
                ->parent('biblioteca_typesense.entity_mapper.abstract')
                ->bind('$className', $collection['entity'])
                ->bind('$mappingConfig', $collection['mapping'])
                // The mapper attribute is used as index and reference the collection.
                ->tag(StandaloneMapperInterface::TAG_NAME, ['mapper' => $name])
                ->private()
                ->autowire();

            // Make sure the automatic mapper is available in the service locator
            $entityServiceLocator[$name] = new Reference($id);
        }

        // Set the entity mapping parameter
        $containerConfigurator->parameters()->set('biblioteca_typesense.config.entity_mapping', $entityMapping);
    }

    public function toCamelCase(string $input): string
    {
        // Replace non-alphanumeric characters with a space
        $input = preg_replace('/[^a-zA-Z0-9]+/', ' ', $input);

        // Capitalize the first letter of each word and remove spaces
        $camelCased = str_replace(' ', '', ucwords((string) $input));

        // Ensure the first letter is lowercase
        return lcfirst($camelCased);
    }
}
