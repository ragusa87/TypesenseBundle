<?php

namespace Biblioteca\TypesenseBundle\Search\Hydrate;

use Biblioteca\TypesenseBundle\Search\Results\SearchResults;
use Biblioteca\TypesenseBundle\Search\Results\SearchResultsHydrated;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @template T of object
 *
 * @implements HydrateSearchResultInterface<T>
 */
class HydrateSearchResult implements HydrateSearchResultInterface
{
    private ?string $primaryKeyOverride = null;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param class-string<T> $class
     *
     * @return SearchResultsHydrated<T>
     *
     * @throws \Exception
     */
    public function hydrate(string $class, SearchResults $searchResults): SearchResultsHydrated
    {
        // Fetch the primary key of the entity
        $classMetadata = $this->entityManager->getClassMetadata($class);
        // TODO Support of composed primary keys ?
        $primaryKey = $classMetadata->isIdentifierComposite ? null : $classMetadata->getSingleIdReflectionProperty();
        $primaryKeyName = ($this->primaryKeyOverride ?? $primaryKey?->getName()) ?? 'id';

        $hits = $searchResults['hits'] ?? [];
        $ids = array_map(function (mixed $result) use ($primaryKeyName): ?int {
            if (!is_array($result) || !is_array($result['document']) || !is_scalar($result['document'][$primaryKeyName] ?? null)) {
                return null;
            }

            return (int) $result['document'];
        }, is_array($hits) ? $hits : []);
        $ids = array_filter($ids);

        if ($ids === []) {
            /** @var SearchResultsHydrated<T> $result */
            $result = new SearchResultsHydrated($searchResults, []);

            return $result;
        }

        $entityRepository = $this->entityManager->getRepository($class);
        if ($entityRepository instanceof HydrateRepositoryInterface) {
            /** @var array<int,T> $collectionData */
            $collectionData = $entityRepository->findByIds($ids)->toArray();

            /** @var SearchResultsHydrated<T> $result */
            $result = new SearchResultsHydrated($searchResults, $collectionData);

            return $result;
        }

        // Build a basic query to fetch the entities by their primary key
        $query = $entityRepository->createQueryBuilder('e')
            ->where('e.'.$primaryKeyName.' IN (:ids)')
            ->indexBy('e', 'e.'.$primaryKeyName)
            ->setParameter('ids', $ids)
            ->getQuery();
        /** @var array<int,T> $hydratedResults */
        $hydratedResults = (array) $query->getResult();

        /** @var SearchResultsHydrated<T> $result */
        $result = new SearchResultsHydrated($searchResults, $hydratedResults);

        return $result;
    }

    /**
     * @return HydrateSearchResult<T>
     */
    public function setPrimaryKeyOverride(?string $primaryKeyOverride): self
    {
        $this->primaryKeyOverride = $primaryKeyOverride;

        return $this;
    }
}
