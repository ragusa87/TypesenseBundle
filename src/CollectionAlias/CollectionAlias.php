<?php

namespace Biblioverse\TypesenseBundle\CollectionAlias;

use Biblioverse\TypesenseBundle\Client\ClientInterface;
use Http\Client\Exception;
use Typesense\Exceptions\TypesenseClientError;

class CollectionAlias implements CollectionAliasInterface
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly string $collectionTemplate = '%s',
    ) {
    }

    public function getName(string $name): string
    {
        $name = sprintf($this->collectionTemplate, $name);

        $date = (new \DateTimeImmutable())->format('Y-m-d-H-i-s');

        return sprintf('%s-%s', $name, $date);
    }

    /**
     * @throws AliasException
     */
    public function switch(string $shortName, string $longName): void
    {
        try {
            // If alias was previously a collection, we delete it (to make sure we can create the alias)
            $collection = $this->client->getCollection($shortName);

            if ($this->collectionExists($shortName)) {
                $collection->delete();
            }

            // Point the alias to the new collection (Note that the old collection is deleted automatically!)
            $this->client->getAliases()->upsert($shortName, ['collection_name' => $longName]);
        } catch (TypesenseClientError|Exception $e) {
            throw new AliasException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Collection->exists() method is not available in typesense-php 4.x.
     */
    private function collectionExists(string $name): bool
    {
        try {
            $this->client->getCollection($name)->retrieve();

            return true;
        } catch (TypesenseClientError|Exception) {
            return false;
        }
    }
}
