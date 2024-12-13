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
