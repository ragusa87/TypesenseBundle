<?php

namespace Biblioteca\TypesenseBundle\Search;

use Biblioteca\TypesenseBundle\Query\SearchQuery;
use Biblioteca\TypesenseBundle\Search\Results\SearchResults;
use Http\Client\Exception;
use Typesense\Exceptions\TypesenseClientError;

interface SearchInterface
{
    /**
     * @throws Exception
     * @throws TypesenseClientError
     */
    public function search(string $collectionName, SearchQuery $query): SearchResults;
}
