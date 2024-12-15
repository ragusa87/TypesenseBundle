<?php

namespace Biblioteca\TypesenseBundle\Search;

use Biblioteca\TypesenseBundle\Query\SearchQuery;
use Biblioteca\TypesenseBundle\Search\Hydrate\HydrateSearchResultInterface;
use Biblioteca\TypesenseBundle\Search\Results\SearchResults;
use Biblioteca\TypesenseBundle\Search\Results\SearchResultsHydrated;

/**
 * @template T
 *
 * @implements SearchCollectionInterface<T>
 */
class SearchCollection implements SearchCollectionInterface
{
    /**
     * @param class-string<T>                 $entityClass
     * @param HydrateSearchResultInterface<T> $hydrateSearchResult
     */
    public function __construct(
        private readonly string $collectionName,
        private readonly string $entityClass,
        private readonly Search $executeSearchQuery,
        private readonly HydrateSearchResultInterface $hydrateSearchResult,
    ) {
    }

    public function searchRaw(SearchQuery $query): SearchResults
    {
        return $this->executeSearchQuery->search($this->collectionName, $query);
    }

    public function search(SearchQuery $query): SearchResultsHydrated
    {
        $searchResult = $this->executeSearchQuery->search($this->collectionName, $query);

        return $this->hydrateSearchResult->hydrate($this->entityClass, $searchResult);
    }
}
