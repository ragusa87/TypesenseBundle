<?php

namespace Biblioteca\TypesenseBundle\Mapper\Options;

class CollectionOptions implements CollectionOptionsInterface
{
    /**
     * @param list<string>|null $tokenSeparators
     * @param list<string>|null $symbolsToIndex
     **/
    public function __construct(
        public ?array $tokenSeparators = null,
        public ?array $symbolsToIndex = null,
        public ?string $defaultSortingField = null,
    ) {
    }

    /**
     * @param array{'token_separators'?: list<string>|null, 'symbols_to_index'?: list<string>|null, 'default_sorting_field'?: string|null} $collectionOption
     */
    public static function fromArray(array $collectionOption): self
    {
        return new self(
            tokenSeparators: $collectionOption['token_separators'] ?? null,
            symbolsToIndex: $collectionOption['symbols_to_index'] ?? null,
            defaultSortingField: $collectionOption['default_sorting_field'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter(
            [
                'token_separators' => $this->tokenSeparators,
                'symbols_to_index' => $this->symbolsToIndex,
                'default_sorting_field' => $this->defaultSortingField,
            ], fn ($value) => $value !== null);
    }
}
