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
        private readonly Search $search,
        private readonly HydrateSearchResultInterface $hydrateSearchResult,
    ) {
    }

    public function searchRaw(SearchQuery $searchQuery): SearchResults
    {
        return $this->search->search($this->collectionName, $searchQuery);
    }

    public function search(SearchQuery $searchQuery): SearchResultsHydrated
    {
        $searchResults = $this->search->search($this->collectionName, $searchQuery);

        return $this->hydrateSearchResult->hydrate($this->entityClass, $searchResults);
    }
}
