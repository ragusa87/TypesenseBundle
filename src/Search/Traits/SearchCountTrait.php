<?php

namespace Biblioteca\TypesenseBundle\Search\Traits;

trait SearchCountTrait
{
    public function count(): int
    {
        if (!$this->offsetExists('hits')) {
            return 0;
        }

        return count((array) $this->data['hits']);
    }
}
