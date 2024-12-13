<?php

namespace Biblioteca\TypesenseBundle\CollectionName;

interface NameInterface
{
    public function getName(string $name): string;
}
