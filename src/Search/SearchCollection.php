<?php

namespace Biblioteca\TypesenseBundle\Search;

use Biblioteca\TypesenseBundle\Query\SearchQuery;
use Biblioteca\TypesenseBundle\Search\Hydrate\HydrateSearchResultInterface;
use Biblioteca\TypesenseBundle\Search\Results\SearchResults;
use Biblioteca\TypesenseBundle\Search\Results\SearchResultsHydrated;

class SearchCollection implements SearchCollectionInterface
{
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
