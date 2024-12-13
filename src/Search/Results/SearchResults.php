<?php

namespace Biblioteca\TypesenseBundle\Search\Results;

use Biblioteca\TypesenseBundle\Search\Traits\FoundCountTrait;
use Biblioteca\TypesenseBundle\Search\Traits\SearchCountTrait;
use Biblioteca\TypesenseBundle\Search\Traits\SearchFacetTrait;
use Biblioteca\TypesenseBundle\Utils\ArrayAccessTrait;

/**
 * @implements \ArrayAccess<string, mixed>
 */
class SearchResults implements \ArrayAccess, \IteratorAggregate, \Countable
{
    use ArrayAccessTrait;
    use SearchFacetTrait;
    use SearchCountTrait;
    use FoundCountTrait;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * @return \Traversable<int, array>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator(array_map(fn ($hits): mixed => $hits['document'], $this->data['hits'] ?? []));
    }
}
