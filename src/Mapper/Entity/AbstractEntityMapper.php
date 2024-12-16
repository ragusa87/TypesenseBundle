<?php

namespace Biblioteca\TypesenseBundle\Mapper\Entity;

use Biblioteca\TypesenseBundle\Mapper\Mapping\Mapping;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * @template T of Object
 *
 * @implements EntityMapperInterface<T>
 */
abstract class AbstractEntityMapper implements EntityMapperInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    abstract public function getMapping(): Mapping;

    public function getData(): \Generator
    {
        $identifiers = $this->entityManager->getClassMetadata($this->getClassName())->getIdentifier();
        $queryBuilder = $this->entityManager->getRepository($this->getClassName())->createQueryBuilder('entity')
            ->select('entity');
        foreach ($identifiers as $identifier) {
            $queryBuilder->addOrderBy('entity.'.$identifier, 'ASC');
        }

        $this->alterQueryBuilder($queryBuilder);

        $query = $queryBuilder->getQuery();

        /** @var T $data */
        foreach ($query->toIterable() as $data) {
            yield $this->transform($data);
        }
    }

    public function getDataCount(): ?int
    {
        $queryBuilder = $this->entityManager->getRepository($this->getClassName())->createQueryBuilder('entity');
        $identifiers = $this->entityManager->getClassMetadata($this->getClassName())->getIdentifier();

        $countSelect = [];
        foreach ($identifiers as $identifier) {
            $countSelect[] = sprintf('COUNT(entity.%s)', $identifier);
        }
        $queryBuilder->select(implode(' + ', $countSelect));

        $this->alterQueryBuilder($queryBuilder);

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @param object&T $entity
     *
     * @return array<string, mixed>
     */
    abstract public function transform(object $entity): array;

    protected function alterQueryBuilder(QueryBuilder $queryBuilder): void
    {
        // Override this method to alter the query builder, for the fetch and count.
    }

    public function support(object $entity): bool
    {
        return $entity::class === $this->getClassName();
    }

    /**
     * @return class-string<T>
     */
    abstract public function getClassName(): string;
}
