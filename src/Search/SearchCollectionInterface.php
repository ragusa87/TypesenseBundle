<?php

namespace Biblioteca\TypesenseBundle\Search;

use Biblioteca\TypesenseBundle\Exception\SearchException;
use Biblioteca\TypesenseBundle\Query\SearchQuery;
use Biblioteca\TypesenseBundle\Search\Results\SearchResults;
use Biblioteca\TypesenseBundle\Search\Results\SearchResultsHydrated;

/**
 * @template T
 */
interface SearchCollectionInterface
{
    /**
     * @return SearchResultsHydrated<T>
     *
     * @throws SearchException
     */
    public function search(SearchQuery $query): SearchResultsHydrated;

    public function searchRaw(SearchQuery $query): SearchResults;
}
