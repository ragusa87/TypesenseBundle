<?php

namespace Biblioteca\TypesenseBundle\Search\Hydrate;

use Doctrine\Common\Collections\Collection;

/**
 * @template T of object
 */
interface HydrateRepositoryInterface
{
    /**
     * @param int[] $ids
     *
     * @return Collection<int, T>
     */
    public function findByIds(array $ids): Collection;
}
