<?php

namespace Biblioteca\TypesenseBundle\Search;

use Biblioteca\TypesenseBundle\Exception\SearchException;
use Biblioteca\TypesenseBundle\Query\SearchQuery;
use Biblioteca\TypesenseBundle\Search\Results\SearchResults;

interface SearchInterface
{
    /**
     * @throws SearchException
     */
    public function search(string $collectionName, SearchQuery $searchQuery): SearchResults;
}
