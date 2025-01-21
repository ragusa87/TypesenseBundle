<?php

namespace Biblioverse\TypesenseBundle\Mapper\Converter;

interface ToTypesenseInterface
{
    /**
     * @return array<string, mixed>
     */
    public function toTypesense(): array;
}
