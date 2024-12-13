<?php

namespace Biblioteca\TypesenseBundle\Query;

interface VectorQueryInterface
{
    /**
     * @return array<string,mixed>
     */
    public function toArray(): array;
}
