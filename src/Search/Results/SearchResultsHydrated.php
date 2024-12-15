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
     * @param SearchResults<T> $searchResults
     *
     * @throws \Exception
     */
    public function __construct(SearchResults $searchResults, array $hydratedResults = [])
    {
        $this->data = $searchResults->toArray();
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
     * @return \Traversable<int, T>
     */
    public function getIterator(): \Traversable
    {
        if (!$this->offsetExists('hydrated') || !is_array($this->data['hydrated'])) {
            return new \ArrayIterator([]);
        }
        $ids = array_keys($this->data['hydrated']);
        $ids = array_map('intval', $ids);
        /** @var array<int,T> $values */
        $values = array_values($this->data['hydrated']);

        return new \ArrayIterator(array_combine($ids, $values));
    }
}
