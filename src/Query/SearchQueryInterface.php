<?php

namespace Biblioverse\TypesenseBundle\Query;

interface SearchQueryInterface
{
    /**
     * @return array<string,mixed>
     */
    public function toArray(): array;
}
