<?php

namespace Biblioteca\TypesenseBundle\Search\Results;

use Biblioteca\TypesenseBundle\Search\Traits\FoundCountTrait;
use Biblioteca\TypesenseBundle\Search\Traits\HighlightTrait;
use Biblioteca\TypesenseBundle\Search\Traits\SearchCountTrait;
use Biblioteca\TypesenseBundle\Search\Traits\SearchFacetTrait;
use Biblioteca\TypesenseBundle\Utils\ArrayAccessTrait;

/**
 * @implements \ArrayAccess<string, mixed>
 * @implements \IteratorAggregate<int, array<string,mixed>>
 */
class SearchResults implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * @use ArrayAccessTrait<string, mixed>
     */
    use ArrayAccessTrait;
    use SearchFacetTrait;
    use SearchCountTrait;
    use FoundCountTrait;
    use HighlightTrait;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * @return \Traversable<int, array<string,mixed>>
     */
    public function getIterator(): \Traversable
    {
        $data = [];
        if ($this->offsetExists('hits') && is_array($this->data['hits'])) {
            $data = $this->data['hits'];
        }
        /** @var array<int, array<string, mixed>> $data */
        $data = array_filter(array_map(function (mixed $hits): mixed {
            if (!is_array($hits) || $hits === [] || !isset($hits['document'])) {
                return null;
            }

            return $hits['document'];
        }, $data), fn ($a): bool => $a !== null);

        return new \ArrayIterator($data);
    }
}
