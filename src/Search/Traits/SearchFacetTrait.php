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
        if (!$this->offsetExists('facet_counts') || !is_array($this->data['facet_counts']) || $this->data['facet_counts'] === []) {
            return [];
        }

        /** @var FacetCount[] $data */
        $data = array_map(fn ($value) =>
            /* @var FacetCount $value */
            $value, $this->data['facet_counts']);

        return $data;
    }
}
