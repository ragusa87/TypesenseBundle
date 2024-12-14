<?php

namespace Biblioteca\TypesenseBundle\Search\Hydrate;

use Biblioteca\TypesenseBundle\Search\Results\SearchResults;
use Biblioteca\TypesenseBundle\Search\Results\SearchResultsHydrated;

/**
 * @template T
 */
interface HydrateSearchResultInterface
{
    /**
     * @param class-string<T> $class
     *
     * @return SearchResultsHydrated<T>
     */
    public function hydrate(string $class, SearchResults $results): SearchResultsHydrated;
}
