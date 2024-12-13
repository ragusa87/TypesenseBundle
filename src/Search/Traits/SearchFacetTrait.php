<?php

namespace Biblioteca\TypesenseBundle\Search\Traits;

/**
 * @phpstan-type FacetCountItem array{count: int, value: string, highlighted: string}
 * @phpstan-type FacetCount array{field_name: string, sampled: bool, stats: array{total_values: int}, counts: FacetCountItem[]}
 */
trait SearchFacetTrait
{
    /**
     * @return FacetCount[]
     */
    public function getFacetCounts(): array
    {
        return $this->data['facet_counts'];
    }
}
