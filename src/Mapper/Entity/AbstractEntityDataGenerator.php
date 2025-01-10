<?php

namespace Biblioteca\TypesenseBundle\Mapper\Entity;

use Biblioteca\TypesenseBundle\Mapper\Converter\Exception\ValueConversionException;
use Biblioteca\TypesenseBundle\Mapper\Converter\Exception\ValueExtractorException;
use Biblioteca\TypesenseBundle\Mapper\DataGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * @template T of Object
 *
 * @implements EntityTransformerInterface<T>
 */
abstract class AbstractEntityDataGenerator implements DataGeneratorInterface, EntityTransformerInterface
{
    /**
     * @param class-string<T> $className
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly string $className,
    ) {
    }

    /**
     * @throws ValueExtractorException
     * @throws ValueConversionException
     */
    public function getData(): \Generator
    {
        $identifiers = $this->entityManager->getClassMetadata($this->className)->getIdentifier();
        $queryBuilder = $this->entityManager->getRepository($this->className)->createQueryBuilder('entity')
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
        $queryBuilder = $this->entityManager->getRepository($this->className)->createQueryBuilder('entity');
        $identifiers = $this->entityManager->getClassMetadata($this->className)->getIdentifier();

        $countSelect = [];
        foreach ($identifiers as $identifier) {
            $countSelect[] = sprintf('COUNT(entity.%s)', $identifier);
        }
        $queryBuilder->select(implode(' + ', $countSelect));

        $this->alterQueryBuilder($queryBuilder);

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    protected function alterQueryBuilder(QueryBuilder $queryBuilder): void
    {
    }

    abstract public function transform(object $entity): array;
}
