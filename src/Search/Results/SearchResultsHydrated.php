<?php

namespace Biblioteca\TypesenseBundle\Search\Results;

use Biblioteca\TypesenseBundle\Search\Traits\FoundCountTrait;
use Biblioteca\TypesenseBundle\Search\Traits\SearchCountTrait;
use Biblioteca\TypesenseBundle\Search\Traits\SearchFacetTrait;
use Biblioteca\TypesenseBundle\Utils\ArrayAccessTrait;

/**
 * @template T
 *
 * @implements \ArrayAccess<string, mixed>
 * @implements \IteratorAggregate<int, T>
 */
class SearchResultsHydrated implements \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * @use ArrayAccessTrait<string, mixed>
     */
    use ArrayAccessTrait;
    use FoundCountTrait;
    use SearchCountTrait;
    use SearchFacetTrait;

    /**
     * @param array<int, T>    $hydratedResults
     * @param SearchResults<T> $results
     *
     * @throws \Exception
     */
    public function __construct(SearchResults $results, array $hydratedResults = [])
    {
        $this->data = $results->toArray();
        $this->setHydratedResults($hydratedResults);
    }

    /**
     * @param array<int, T> $data
     *
     * @return SearchResultsHydrated<T>
     */
    public function setHydratedResults(array $data): self
    {
        $this->data['hydrated'] = $data;

        return $this;
    }

    /**
     * @return \Traversable<int|string, T>
     */
    public function getIterator(): \Traversable
    {
        if (!$this->offsetExists('hydrated')) {
            return new \ArrayIterator([]);
        }

        return new \ArrayIterator((array) $this->data['hydrated']);
    }
}
