<?php

namespace Biblioverse\TypesenseBundle\Mapper\Entity\Identifier;

interface EntityIdentifierInterface
{
    /**
     * @return array{id: string}
     */
    public function getIdentifiersValue(object $entity): array;
}
