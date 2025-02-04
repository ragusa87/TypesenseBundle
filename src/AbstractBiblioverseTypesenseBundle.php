<?php

namespace Biblioverse\TypesenseBundle;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

abstract class AbstractBiblioverseTypesenseBundle extends AbstractBundle
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
            ->booleanNode('auto_update')
                ->info('Automatically update/remove entities from the index on flush')
                ->defaultTrue()
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
                                ->arrayNode('token_separators')
                                    ->defaultValue([])
                                    ->scalarPrototype()->end()
                                ->end()
                                ->arrayNode('symbols_to_index')
                                    ->defaultValue([])
                                    ->scalarPrototype()->end()
                                ->end()
                                ->scalarNode('default_sorting_field')
                                    ->defaultValue(null)
                                ->end()
                                ->arrayNode('metadata')
                                    ->defaultValue([])
                                    ->scalarPrototype()->end()
                                ->end()
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
                                        ->booleanNode('optional')
                                            ->defaultNull()
                                        ->end()
                                        ->booleanNode('facet')
                                            ->defaultNull()
                                        ->end()
                                        ->booleanNode('mapped')
                                            ->defaultTrue()
                                        ->end()
                                        ->scalarNode('entity_attribute')
                                            ->defaultNull()
                                        ->end()
                                        ->booleanNode('drop')
                                            ->defaultNull()
                                        ->end()
                                        ->booleanNode('index')
                                            ->defaultNull()
                                        ->end()
                                        ->booleanNode('infix')
                                            ->defaultNull()
                                        ->end()
                                        ->booleanNode('rangeIndex')
                                            ->defaultNull()
                                        ->end()
                                        ->booleanNode('sort')
                                            ->defaultNull()
                                        ->end()
                                        ->booleanNode('stem')
                                            ->defaultNull()
                                        ->end()
                                        ->booleanNode('store')
                                            ->defaultNull()
                                        ->end()
                                        ->integerNode('numDim')
                                            ->defaultNull()
                                        ->end()
                                        ->scalarNode('locale')
                                            ->defaultNull()
                                        ->end()
                                        ->scalarNode('reference')
                                            ->defaultNull()
                                        ->end()
                                        ->scalarNode('vecDist')
                                            ->defaultNull()
                                        ->end()
                                        ->arrayNode('embed')
                                            ->children()
                                                ->arrayNode('from')
                                                    ->defaultValue([])->scalarPrototype()->end()
                                                ->end()
                                                ->arrayNode('model_config')
                                                    ->children()
                                                        ->scalarNode('model_name')->isRequired()->end()
                                                        ->scalarNode('api_key')->defaultNull()->end()
                                                        ->scalarNode('url')->defaultNull()->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
