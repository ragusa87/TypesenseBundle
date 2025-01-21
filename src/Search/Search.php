<?php

namespace Biblioverse\TypesenseBundle\Search;

use Biblioverse\TypesenseBundle\Client\ClientInterface;
use Biblioverse\TypesenseBundle\Exception\SearchException;
use Biblioverse\TypesenseBundle\Query\SearchQuery;
use Biblioverse\TypesenseBundle\Search\Results\SearchResults;
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
