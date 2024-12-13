<?php

namespace Biblioteca\TypesenseBundle\Search\Traits;

trait FoundCountTrait
{
    public function found(): int
    {
        if (!$this->offsetExists('found') || !is_scalar($this->data['found'])) {
            return 0;
        }

        return intval($this->data['found']);
    }
}
