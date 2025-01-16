<?php

namespace Biblioteca\TypesenseBundle\Tests\Mapper\Entity;

use Biblioteca\TypesenseBundle\Mapper\Entity\EntityDataGenerator;
use Biblioteca\TypesenseBundle\Mapper\Entity\EntityTransformer;
use Biblioteca\TypesenseBundle\Mapper\Mapping\Mapping;
use Biblioteca\TypesenseBundle\Mapper\Mapping\MappingInterface;
use Biblioteca\TypesenseBundle\Mapper\MappingGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

#[CoversClass(EntityDataGenerator::class)]
class EntityDataGeneratorTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testTransform(): void
    {
        $className = \stdClass::class;
        $data = [
            (object) ['id' => 1, 'name' => 'Entity1'],
            (object) ['id' => 2, 'name' => 'Entity2'],
        ];

        $entityManager = $this->getEntityManager($className, $data);
        $entityDataGenerator = $this->getEntityDataGenerator($entityManager, (new Mapping())
            ->add('id', 'string')
            ->add('name', 'string'), $className);

        $this->assertSame(2, $entityDataGenerator->getDataCount());
        $this->assertTrue($entityDataGenerator->support($data[0]));
        $this->assertSame([
            ['id' => '1', 'name' => 'Entity1'],
            ['id' => '2', 'name' => 'Entity2'],
        ], iterator_to_array($entityDataGenerator->getData()));
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $className
     *
     * @return EntityDataGenerator<T>
     */
    private function getEntityDataGenerator(EntityManagerInterface $entityManager, Mapping $mapping, string $className): EntityDataGenerator
    {
        /**
         * @var EntityTransformer<T> $entityTransformer
         */
        $entityTransformer = new EntityTransformer(new class($mapping) implements MappingGeneratorInterface {
            public function __construct(private readonly Mapping $mapping)
            {
            }

            public function getMapping(): MappingInterface
            {
                return $this->mapping;
            }
        });

        return new EntityDataGenerator($entityManager, $entityTransformer, $className);
    }

    /**
     * @template T
     *
     * @param class-string<T> $entityClass
     * @param array<int, T>   $mockData
     *
     * @throws Exception
     */
    private function getEntityManager(string $entityClass, array $mockData): EntityManagerInterface
    {
        $identifiers = ['id'];

        $entityManager = $this->createMock(EntityManagerInterface::class);

        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata->method('getIdentifier')->willReturn($identifiers);
        $entityManager->method('getClassMetadata')->with($entityClass)->willReturn($classMetadata);

        $repository = $this->createMock(EntityRepository::class);
        $entityManager->method('getRepository')->with($entityClass)->willReturn($repository);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->method('select')->willReturnSelf();

        $dataQuery = $this->createMock(Query::class);

        $dataQuery->method('toIterable')->willReturnCallback(fn () => new \ArrayIterator($mockData));
        $dataQuery->method('getSingleScalarResult')->willReturn(count($mockData));
        $repository->method('createQueryBuilder')->with('entity')->willReturn($queryBuilder);
        $queryBuilder->method('getQuery')->willReturn($dataQuery);

        return $entityManager;
    }
}
