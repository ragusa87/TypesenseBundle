<?php

namespace Biblioteca\TypesenseBundle\Search\Hydrate;

use Biblioteca\TypesenseBundle\Search\Results\SearchResults;
use Biblioteca\TypesenseBundle\Search\Results\SearchResultsHydrated;
use Doctrine\ORM\EntityManagerInterface;

class HydrateSearchResult implements HydrateSearchResultInterface
{
    private ?string $primaryKeyOverride = null;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws \Exception
     */
    public function hydrate(string $class, SearchResults $results): SearchResultsHydrated
    {
        // Fetch the primary key of the entity
        $metadata = $this->entityManager->getClassMetadata($class);
        // TODO Support of composed primary keys ?
        $primaryKey = $metadata->isIdentifierComposite ? null : $metadata->getSingleIdReflectionProperty();
        $primaryKeyName = ($this->primaryKeyOverride ?? $primaryKey?->getName()) ?? 'id';

        $hits = $results['hits'] ?? [];
        $ids = array_map(fn ($result): mixed => $result['document'][$primaryKeyName] ?? null, $hits);
        $ids = array_filter($ids);

        if ($ids === []) {
            return new SearchResultsHydrated($results, []);
        }

        $repository = $this->entityManager->getRepository($class);
        if ($repository instanceof HydrateRepositoryInterface) {
            return $repository->findByIds($ids);
        }

        // Build a basic query to fetch the entities by their primary key
        $query = $repository->createQueryBuilder('e')
            ->where('e.'.$primaryKeyName.' IN (:ids)')
            ->indexBy('e', 'e.'.$primaryKeyName)
            ->setParameter('ids', $ids)
            ->getQuery();
        $hydratedResults = (array) $query->getResult();

        // TODO Handle pagination ?
        return new SearchResultsHydrated($results, $hydratedResults);
    }

    public function setPrimaryKeyOverride(?string $primaryKeyOverride): self
    {
        $this->primaryKeyOverride = $primaryKeyOverride;

        return $this;
    }
}
