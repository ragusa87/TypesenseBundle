<?php

namespace Biblioteca\TypesenseBundle\Search\Results;

use Biblioteca\TypesenseBundle\Search\Traits\FoundCountTrait;
use Biblioteca\TypesenseBundle\Search\Traits\HighlightTrait;
use Biblioteca\TypesenseBundle\Search\Traits\PageTrait;
use Biblioteca\TypesenseBundle\Search\Traits\SearchCountTrait;
use Biblioteca\TypesenseBundle\Search\Traits\SearchFacetTrait;
use Biblioteca\TypesenseBundle\Utils\ArrayAccessTrait;

/**
 * @template T of object
 *
 * @implements \ArrayAccess<string, mixed>
 * @implements \IteratorAggregate<string|int, T>
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
    use HighlightTrait;
    use PageTrait;

    /**
     * @var \Iterator<string|int, T>
     */
    private \Iterator $iterator;

    /**
     * @param array<string|int, T> $hydratedResults
     * @param array<string, mixed> $data
     *
     * @throws \Exception
     */
    private function __construct(array $data, array $hydratedResults = [])
    {
        $this->data = $data;
        $this->setHydratedResults($hydratedResults);
    }

    /**
     * @param array<string|int, T> $data
     *
     * @return SearchResultsHydrated<T>
     */
    public function setHydratedResults(array $data): self
    {
        $this->data['hydrated'] = $data;
        $this->iterator = new \ArrayIterator($data);

        return $this;
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string|int, T> $objects
     *
     * @return SearchResultsHydrated<T>
     */
    public static function fromPayloadAndCollection(array $data, array $objects): self
    {
        return new self($data, $objects);
    }

    /**
     * @param array<string|int, T> $objects
     *
     * @return SearchResultsHydrated<T>
     */
    public static function fromResultAndCollection(SearchResults $searchResults, array $objects): self
    {
        return new self($searchResults->toArray(), $objects);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return SearchResultsHydrated<T>
     */
    public static function fromPayload(array $data): self
    {
        /** @var array<int|string,T> $objects */
        $objects = [];

        return new self($data, $objects);
    }

    /**
     * @return \Iterator<string|int, T>
     */
    public function getIterator(): \Iterator
    {
        $this->iterator->rewind();

        return $this->iterator;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $export = $this->data;
        // Hide the hydrated data from the exported array
        unset($export['hydrated']);

        return $export;
    }
}
