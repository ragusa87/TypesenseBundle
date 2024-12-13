<?php

namespace Biblioteca\TypesenseBundle\Search\Hydrate;

use Doctrine\Common\Collections\Collection;

interface HydrateRepositoryInterface
{
    public function findByIds(array $ids): Collection;
}
