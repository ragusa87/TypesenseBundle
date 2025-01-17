<?php

namespace Biblioteca\TypesenseBundle\Search\Traits;

trait RequestParametersTrait
{
    /**
     * @return array<string, mixed>
     */
    public function getRequestParameters(): array
    {
        if (!$this->offsetExists('request_params') || !is_array($this->data['request_params'])) {
            return [];
        }

        /** @var array<string,mixed> $data */
        $data = $this->data['request_params'];

        return $data;
    }
}
