<?php

namespace Biblioteca\TypesenseBundle\Search\Results;

use Biblioteca\TypesenseBundle\Search\Traits\FoundCountTrait;
use Biblioteca\TypesenseBundle\Search\Traits\SearchCountTrait;
use Biblioteca\TypesenseBundle\Search\Traits\SearchFacetTrait;
use Biblioteca\TypesenseBundle\Utils\ArrayAccessTrait;

/**
 * @template T
 * @implements \ArrayAccess<string, mixed>
 */
class SearchResultsHydrated implements \IteratorAggregate, \Countable, \ArrayAccess
{
    use ArrayAccessTrait;
    use FoundCountTrait;
    use SearchCountTrait;
    use SearchFacetTrait;

    /**
     * @param $hydratedResults iterable<int, T>
     * @throws \Exception
     */
    public function __construct(SearchResults $results, iterable $hydratedResults = [])
    {
        $this->data = $results->toArray();
        $this->setHydratedResults($hydratedResults);
    }

    /**
     * @param iterable<int, T> $data
     */
    public function setHydratedResults(iterable $data): self
    {
        $this->data['hydrated'] = $data;

        return $this;
    }

    /**
     * @return \Traversable<int, T>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->data['hydrated'] ?? []);
    }
}
