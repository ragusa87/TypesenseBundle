<?php

namespace Biblioteca\TypesenseBundle\Populate;

use Biblioteca\TypesenseBundle\Client\ClientInterface;
use Biblioteca\TypesenseBundle\Mapper\Fields\FieldMappingInterface;
use Biblioteca\TypesenseBundle\Mapper\MapperInterface;
use Http\Client\Exception;
use Typesense\Collection;
use Typesense\Exceptions\TypesenseClientError;

class PopulateService
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly int $batchSize = 100,
    ) {
    }

    public function deleteCollection(string $name): void
    {
        $this->client->getCollection($name)->delete();
    }

    public function createCollection(string $collectionName, MapperInterface $mapper): Collection
    {
        $mapping = $mapper->getMapping();

        $payload = array_filter([
            'name' => $collectionName,
            'fields' => array_map(fn (FieldMappingInterface $fieldMapping): array => $fieldMapping->toArray(), $mapping->getFields()),
            'metadata' => $mapping->getMetadata()?->toArray(),
            ...$mapping->getCollectionOptions()?->toArray() ?? [],
        ], fn ($value): bool => !is_null($value));

        try {
            $this->client->getCollections()->create($payload);

            return $this->client->getCollection($collectionName);
        } catch (Exception|TypesenseClientError $e) {
            throw new \RuntimeException('Unable to create collection', 0, $e);
        }
    }

    public function fillCollection(string $name, MapperInterface $mapper): \Generator
    {
        $collection = $this->client->getCollection($name);
        $generator = $mapper->getData();
        foreach ((new BatchGenerator($generator, $this->batchSize))->generate() as $items) {
            $collection->documents->import($items);
            yield from $items;
        }
    }
}
