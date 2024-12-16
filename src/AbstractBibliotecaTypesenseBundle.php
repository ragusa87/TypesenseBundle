<?php

namespace Biblioteca\TypesenseBundle;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

abstract class AbstractBibliotecaTypesenseBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definitionConfigurator): void
    {
        $definitionConfigurator->rootNode()
            ->children()
                ->arrayNode('typesense')
                ->info('Typesense server configuration')
                ->isRequired()
                ->children()
                    ->scalarNode('uri')
                        ->info('The URL of the Typesense server')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('key')
                        ->info('The API key for accessing the Typesense server')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('connection_timeout_seconds')
                        ->defaultValue(5)
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end()
        ->end();

        $this->addCollectionsConfig($definitionConfigurator->rootNode());
    }

    private function addCollectionsConfig(ArrayNodeDefinition $arrayNodeDefinition): void
    {
        $arrayNodeDefinition
            ->children()
                ->arrayNode('collections')
                    ->info('Collection definition')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                    ->children()
                        ->scalarNode('entity')->isRequired()->end()
                        ->scalarNode('name')->end()
                        ->arrayNode('mapping')
                        ->children()
                            ->arrayNode('fields')
                            ->defaultValue([])
                            ->arrayPrototype()
                            ->children()
                                ->scalarNode('name')
                                    ->isRequired()
                                    ->info('The name of the field in the collection.')
                                ->end()
                                ->scalarNode('type')
                                    ->isRequired()
                                    ->info('The type of the field in the collection.')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
            ->end()
        ->end()
        ;
    }
}
