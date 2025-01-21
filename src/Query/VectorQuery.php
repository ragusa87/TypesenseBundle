<?php

namespace Biblioverse\TypesenseBundle\Query;

class VectorQuery implements VectorQueryInterface
{
    /**
     * @param array<float> $queryVector e.g., [0.1, 0.2, 0.3]
     */
    public function __construct(
        private readonly array $queryVector, // e.g., [0.1, 0.2, 0.3]
        private readonly int $numCandidates, // e.g., 100
        private readonly ?float $weight = null, // Optional weight
    ) {
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'query_vector' => $this->queryVector,
            'num_candidates' => $this->numCandidates,
            'weight' => $this->weight,
        ], fn ($value): bool => !is_null($value));
    }
}
