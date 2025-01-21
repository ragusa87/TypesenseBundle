<?php

namespace Biblioteca\TypesenseBundle\Search\Results;

/**
 * @template T of object
 *
 * @extends AbstractSearchResults<T>
 */
class SearchResultsHydrated extends AbstractSearchResults
{
    /**
     * @param array<string|int, T> $hydratedResults
     * @param array<string, mixed> $data
     *
     * @throws \Exception
     */
    private function __construct(array $data, private readonly array $hydratedResults = [])
    {
        parent::__construct($data);
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

    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->hydratedResults);
    }

    public function getResults(): array
    {
        return $this->hydratedResults;
    }
}
