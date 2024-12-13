<?php

namespace Biblioteca\TypesenseBundle\Search\Traits;

trait SearchCountTrait
{
    public function count(): int
    {
        return count($this->data['hits'] ?? []);
    }
}
