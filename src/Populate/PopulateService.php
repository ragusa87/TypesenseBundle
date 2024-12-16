<?php

namespace Biblioteca\TypesenseBundle\Populate;

use Biblioteca\TypesenseBundle\Client\ClientInterface;
use Biblioteca\TypesenseBundle\Mapper\Fields\FieldMappingInterface;
use Biblioteca\TypesenseBundle\Mapper\MapperInterface;
use Biblioteca\TypesenseBundle\Mapper\Mapping\MappingInterface;
use Biblioteca\TypesenseBundle\Type\DataTypeEnum;
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
        $this->throwIfIdIsNotSet($mapping, $collectionName);

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

    /**
     * @param array<string,mixed> $data
     *
     * @return \Generator<array<string,mixed>>
     */
    public function fillData(string $name, array $data): \Generator
    {
        $collection = $this->client->getCollection($name);
        $collection->documents->upsert($data);
        yield $data;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function deleteData(string $name, array $data, ?string $id = null): void
    {
        if (isset($data['id']) && $id === null) {
            $id = $data['id'];
        }

        if ($id === null) {
            throw new \InvalidArgumentException('Entity must be transformed with an \'id\' field');
        }

        $this->client->getCollection($name)->documents[$id]->delete();
    }

    /**
     * @throws \InvalidArgumentException if you do not have an ID in your mapping
     */
    private function throwIfIdIsNotSet(MappingInterface $mapping, string $collectionName): void
    {
        foreach ($mapping->getFields() as $fieldMapping) {
            if ($fieldMapping->getName() === 'id' && $fieldMapping->getType() === DataTypeEnum::STRING->value) {
                return;
            }
        }

        throw new \InvalidArgumentException('The ID field must be set as string in your mapping for '.$collectionName);
    }
}
