<?php

namespace Biblioteca\TypesenseBundle\Search\Traits;

trait HighlightTrait
{
    /**
     * @return array<int,non-empty-array<string,array{field:string, snippet?: string, 'matched_tokens': string[]}>>
     **/
    public function getHighlight(): array
    {
        if (!$this->offsetExists('hits') || !is_array($this->data['hits'])) {
            return [];
        }

        $response = [];
        foreach (array_values($this->data['hits']) as $index => $result) {
            if (!is_array($result) || !isset($result['highlights']) || !is_array($result['highlights'])) {
                continue;
            }
            /** @var array{'field': string, 'snippet'?: string, 'matched_tokens': string[]} $highlight */
            foreach ($result['highlights'] as $highlight) {
                $field = $highlight['field'];
                $response[$index][$field] = $highlight;
            }
        }
        reset($this->data['hits']);

        return $response;
    }
}
