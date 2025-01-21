<?php

namespace Biblioverse\TypesenseBundle\Query;

interface VectorQueryInterface
{
    /**
     * @return array<string,mixed>
     */
    public function toArray(): array;
}
