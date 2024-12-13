<?php

namespace Biblioteca\TypesenseBundle\Search\Traits;

trait FoundCountTrait
{
    public function found(): int
    {
        return intval($this->data['found'] ?? 0);
    }
}
