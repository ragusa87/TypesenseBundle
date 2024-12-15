<?php

namespace Biblioteca\TypesenseBundle\Search;

use Biblioteca\TypesenseBundle\Client\ClientInterface;
use Biblioteca\TypesenseBundle\Exception\SearchException;
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
     * @throws SearchException
     */
    public function search(string $collectionName, SearchQuery $searchQuery): SearchResults
    {
        try {
            /** @var array<string, mixed> $result */
            $result = $this->client->getCollection($collectionName)
                ->documents->search($searchQuery->toArray());

            return new SearchResults($result);
        } catch (TypesenseClientError|Exception $e) {
            throw new SearchException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
