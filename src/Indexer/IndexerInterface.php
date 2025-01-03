<?php

namespace Biblioteca\TypesenseBundle\Indexer;

interface IndexerInterface
{
    public function indexEntity(object $entity): self;

    public function removeEntity(object $entity): self;
}
