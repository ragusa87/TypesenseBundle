<?php

namespace Biblioteca\TypesenseBundle\Search;

use Biblioteca\TypesenseBundle\Client\ClientInterface;
use Biblioteca\TypesenseBundle\Query\SearchQuery;
use Biblioteca\TypesenseBundle\Search\Results\SearchResults;
use Http\Client\Exception;
use Typesense\Exceptions\TypesenseClientError;

class Search implements SearchInterface
{
    public function __construct(private readonly ClientInterface $client)
    {
    }

    /**
     * @throws Exception
     * @throws TypesenseClientError
     */
    public function search(string $collectionName, SearchQuery $query): SearchResults
    {
        return new SearchResults($this->client->getCollections()->__get($collectionName)
            ->documents->search($query->toArray()));
    }
}
