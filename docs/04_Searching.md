To use search results, you have two options:

1. Search with Hydrated results (your entities are hydrated from the search results)
2. Search without hydrated results (you get the raw Typesense results)

See also classes `SearchResults` vs `SearchResultsHydrated` to get more information about the result.


## 1. Search with Hydrated results
Every mapping that you have configured is automatically available as a `SearchCollectionInterface` service.

To use it, you can inject the service in your controller or service.
The name is composed of the collection name with the `Search` suffix.

Example: Given you have declared a collection named `books`, you can inject the service implementing the `SearchCollectionInterface` with the name `searchBooks`.

```php
use Biblioteca\TypesenseBundle\Search\SearchCollectionInterface;

class SearchHelper
{
    public function construct(private SearchCollectionInterface $searchBooks)
    {
    }
}
```

As an alternative, you can use the service id: `biblioteca_typesense.collection.<name>` where `<name>` is the collection name.


## Iterate over the hydrated results

```php
$searchResults = $search->search($query);

foreach ($searchResults as $result) {
    echo $result; // Your entity
}
```
You can use `searchRaw` to skip the hydration and get the raw Typesense result.


## 2. Search without hydrated results

Use the service `SearchInterface`:
```php
use Biblioteca\TypesenseBundle\Search\SearchInterface;
class SearchHelper
{
    public function construct(private SearchInterface $search)
    {
    }
}
```
## Iterate over raw results

```php
$searchResults = $search->search($query);

foreach ($searchResults as $result) {
    echo $result; // Your document
}
```

# Build the search query

To build the search query, you can use the `Biblioteca\TypesenseBundle\Query\SearchQuery` class.
There is a constructor with named parameters to help you build the query, it matches the Typesense API.

> Please always use named parameters. The order will likely change in the future.

```php
<?php
use Biblioteca\TypesenseBundle\Query\SearchQuery;

$query = new SearchQuery(q: 'my search', queryBy: 'name', filterBy: 'owner', sortBy: 'name:desc');
```




# Pagination

Pagination is automatically provided by the Typesense API if you use the `page` and `per_page` parameters in the query.

```php

<?php
use Biblioteca\TypesenseBundle\Query\SearchQuery;

$query = new SearchQuery(q: 'my search', queryBy: 'name', perPage: 10);

```
On the result, you can for example use `getTotalPages()` and `getPage()`.

```php