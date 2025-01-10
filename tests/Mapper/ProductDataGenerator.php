<?php

namespace Biblioteca\TypesenseBundle\Tests\Mapper;

use Biblioteca\TypesenseBundle\Mapper\Entity\AbstractEntityDataGenerator;
use Biblioteca\TypesenseBundle\Mapper\Mapping\Mapping;
use Biblioteca\TypesenseBundle\Mapper\MappingGeneratorInterface;
use Biblioteca\TypesenseBundle\Mapper\StandaloneCollectionManagerInterface;
use Biblioteca\TypesenseBundle\Tests\Entity\Product;
use Biblioteca\TypesenseBundle\Type\DataTypeEnum;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends AbstractEntityDataGenerator<Product>
 */
class ProductDataGenerator extends AbstractEntityDataGenerator implements MappingGeneratorInterface, StandaloneCollectionManagerInterface
{
    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        parent::__construct($entityManager, Product::class);
    }

    public static function getName(): string
    {
        return 'products';
    }

    public function getMapping(): Mapping
    {
        return (new Mapping())
            ->add(name: 'id', type: DataTypeEnum::STRING)
            ->add(name: 'name', type: DataTypeEnum::STRING)
        ;
    }

    public function transform(object $entity): array
    {
        return [
            'id' => (string) $entity->id,
            'name' => $entity->name,
        ];
    }

    public function support(object $entity): bool
    {
        return $entity::class === Product::class;
    }
}
