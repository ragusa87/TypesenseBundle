<?php

namespace Biblioteca\TypesenseBundle\CollectionName;

use Biblioteca\TypesenseBundle\Client\ClientInterface;

class AliasName implements NameInterface
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly string $collectionTemplate = '%s',
    ) {
    }

    public function getName(string $name): string
    {
        $name = sprintf($this->collectionTemplate, $name);
        if (!$this->isAliasEnabled()) {
            return $name;
        }
        $date = (new \DateTimeImmutable())->format('Y-m-d-H-i-s');

        return sprintf('%s-%s', $name, $date);
    }

    public function isAliasEnabled(): bool
    {
        return true;
    }

    public function switch(string $shortName, string $longName): void
    {
        if (!$this->isAliasEnabled()) {
            return;
        }

        // If alias was previously a collection, we delete it (to make sure we can create the alias)
        if ($this->client->getCollections()->__get($shortName)->exists()) {
            $this->client->getCollections()->__get($shortName)->delete();
        }

        // Point the alias to the new collection (Note that the old collection is deleted automatically!)
        $this->client->getAliases()->upsert($shortName, ['collection_name' => $longName]);
    }
}
