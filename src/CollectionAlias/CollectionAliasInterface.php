<?php

namespace Biblioteca\TypesenseBundle\CollectionAlias;

interface CollectionAliasInterface
{
    public function getName(string $name): string;

    public function switch(string $shortName, string $longName): void;
}