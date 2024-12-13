<?php

namespace Biblioteca\TypesenseBundle\Mapper;

use Biblioteca\TypesenseBundle\Mapper\Mapping\Mapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;

/**
 * @template T of Object
 */
abstract class AbstractEntityMapper implements MapperInterface
{
    public function __construct(
        private readonly ObjectRepository $repository,
    ) {
    }

    abstract public function getMapping(): Mapping;

    public function getData(): \Generator
    {
        $queryBuilder = $this->repository->createQueryBuilder('entity')
            ->select('entity')
            ->orderBy('entity.id', 'ASC');

        $this->alterQueryBuilder($queryBuilder);

        $query = $queryBuilder->getQuery();

        /** @var T $data */
        foreach ($query->toIterable() as $data) {
            yield $this->transform($data);
        }
    }

    public function getDataCount(): ?int
    {
        $queryBuilder = $this->repository->createQueryBuilder('entity')
            ->select('COUNT(distinct entity.id)');

        $this->alterQueryBuilder($queryBuilder);

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @param object&T $data
     * @return array<string, mixed>
     */
    abstract public function transform(object $data): array;

    protected function alterQueryBuilder(QueryBuilder $queryBuilder): void
    {
    }
}
