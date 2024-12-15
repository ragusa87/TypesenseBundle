<?php

namespace Biblioteca\TypesenseBundle;

use Biblioteca\TypesenseBundle\Mapper\MapperInterface;
use Biblioteca\TypesenseBundle\Search\SearchCollectionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

class BibliotecaTypesenseBundle extends AbstractBibliotecaTypesenseBundle
{
    /**
     * @param array{typesense: array{uri: string, key: string, connection_timeout_seconds: int}, collections: array<string, array{entity: string, name?: string}>} $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->registerForAutoconfiguration(MapperInterface::class)
            ->addTag(MapperInterface::TAG_NAME);

        /** @var iterable<string,mixed> $typesenseConfig */
        $typesenseConfig = $config['typesense'];
        foreach ($typesenseConfig as $key => $value) {
            $container->parameters()->set('biblioteca_typesense.config.'.$key, $value);
        }

        $container->import(__DIR__.'/Resources/config/services.yaml');

        $this->loadCollection($config['collections'], $container, $builder);
    }

    /**
     * @param array<string, array{entity: string, name?: string}> $collections
     */
    public function loadCollection(array $collections, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        foreach ($collections as $name => $collection) {
            $id = 'biblioteca_typesense.collection.'.$name;
            $container->services()
                ->set($id)
                ->parent('biblioteca_typesense.collection.abstract')
                ->arg(0, $name)
                ->arg(1, $collection['entity'])
                ->public()
                ->autowire();

            // You can inject ExecuteCollectionSearchResultInterface in your service with the name "SearchBooks", given the collection name is "books".
            $bindingName = '$'.$this->toCamelCase('Search '.$name);
            $container->services()->defaults()
                ->alias(SearchCollectionInterface::class.' '.$bindingName, new Reference($id));
        }
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
