<?php

namespace Biblioteca\TypesenseBundle\Query;

use Biblioteca\TypesenseBundle\Type\InfixEnum;

class SearchQuery implements SearchQueryInterface
{
    private readonly ?string $infix;
    private readonly ?string $prefix;

    private readonly ?string $stopwords;

    /**
     * @param string|InfixEnum[]|null  $infix
     * @param bool|bool[]|null         $prefix
     * @param string[]|null            $stopwords
     * @param array<string,mixed>|null $hiddenHits
     */
    public function __construct(
        private readonly string $q,
        private readonly string $queryBy,
        private readonly ?string $filterBy = null,
        private readonly ?string $sortBy = null,
        string|array|null $infix = null,
        bool|array|null $prefix = null,
        private readonly bool $preSegmentedQuery = false,
        private readonly ?string $preset = null,
        private readonly ?VectorQueryInterface $vectorQuery = null,
        ?array $stopwords = null,
        private readonly ?string $facetBy = null,
        private readonly ?string $facetQuery = null,
        private readonly ?int $remoteEmbeddingTimeoutMs = null,
        private readonly ?int $remoteEmbeddingBatchSize = null,
        private readonly ?int $remoteEmbeddingNumTries = null,
        private readonly ?string $highlightFields = null,
        private readonly ?int $highlightAffixNumTokens = null,
        private readonly ?string $highlightStartTag = null,
        private readonly ?string $highlightEndTag = null,
        private readonly ?string $groupBy = null,
        private readonly ?int $groupLimit = null,
        private readonly ?int $numTypos = null,
        private readonly ?int $page = null,
        private readonly ?int $perPage = null,
        private readonly ?int $maxFacetValues = null,
        private readonly ?int $minLen1Typo = null,
        private readonly ?int $minLen2Typo = null,
        private readonly ?float $dropTokensThreshold = null,
        private readonly ?array $hiddenHits = null,
        private readonly ?string $excludeFields = null,
        private readonly ?VoiceQueryInterface $voiceQuery = null,
    ) {
        $this->infix = $this->convertArray($infix, fn ($infix) => $infix instanceof InfixEnum ? $infix->value : $infix, InfixEnum::class);

        if (is_bool($prefix)) {
            $prefix = (array) $prefix;
        }

        $this->prefix = $prefix === [] || $prefix === null ? null :
            implode(',', array_map(fn (bool $value): string => $value ? 'true' : 'false', $prefix));
        $this->stopwords = $stopwords === null || $stopwords === [] ? null : implode(',', $stopwords);

        // Check incompatible combinations
        if ($this->vectorQuery instanceof VectorQueryInterface && $this->infix !== null) {
            throw new \InvalidArgumentException('Cannot set both infix and vectorQuery');
        }
    }

    private function convertArray(mixed $values, callable $convert, ?string $className = null): ?string
    {
        if (!is_array($values)) {
            if ($values === null) {
                return null;
            }
            $values = $convert($values);
            if ($values === null) {
                return null;
            }
            if (!is_scalar($values)) {
                throw new \InvalidArgumentException('Expected scalar value');
            }

            return (string) $values;
        }
        foreach ($values as $value) {
            if ($className !== null && !$value instanceof $className) {
                throw new \InvalidArgumentException(sprintf('Expected type %s, got %s', $className, get_debug_type($value)));
            }
        }

        return $values === [] ? null : implode(',', array_map($convert, $values));
    }

    public function toArray(): array
    {
        return array_filter([
            'q' => $this->q,
            'query_by' => $this->queryBy,
            'filter_by' => $this->filterBy,
            'sort_by' => $this->sortBy,
            'drop_tokens_threshold' => $this->dropTokensThreshold,
            'facet_by' => $this->facetBy,
            'facet_query' => $this->facetQuery,
            'group_by' => $this->groupBy,
            'group_limit' => $this->groupLimit,
            'hidden_hits' => $this->hiddenHits,
            'highlight_affix_num_tokens' => $this->highlightAffixNumTokens,
            'highlight_end_tag' => $this->highlightEndTag,
            'highlight_fields' => $this->highlightFields,
            'highlight_start_tag' => $this->highlightStartTag,
            'infix' => $this->infix,
            'max_facet_values' => $this->maxFacetValues,
            'min_len_1typo' => $this->minLen1Typo,
            'min_len_2typo' => $this->minLen2Typo,
            'num_typos' => $this->numTypos,
            'page' => $this->page,
            'per_page' => $this->perPage,
            'pre_segmented_query' => $this->preSegmentedQuery,
            'prefix' => $this->prefix,
            'preset' => $this->preset,
            'remote_embedding_batch_size' => $this->remoteEmbeddingBatchSize,
            'remote_embedding_num_tries' => $this->remoteEmbeddingNumTries,
            'remote_embedding_timeout_ms' => $this->remoteEmbeddingTimeoutMs,
            'stopwords' => $this->stopwords,
            'vector_query' => $this->vectorQuery?->toArray(),
            'exclude_fields' => $this->excludeFields,
            'voice_query' => $this->voiceQuery instanceof VoiceQueryInterface ? (string) $this->voiceQuery : null,
        ], fn (mixed $value): bool => !is_null($value));
    }

    public function getQ(): string
    {
        return $this->q;
    }

    public function getQueryBy(): string
    {
        return $this->queryBy;
    }
}
