<?php

namespace Biblioteca\TypesenseBundle\Mapper\Converter;

interface ToTypesenseInterface
{
    /**
     * @return array<string, mixed>
     */
    public function toTypesense(): array;
}
