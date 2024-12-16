<?php

namespace Biblioteca\TypesenseBundle\Tests\Mapper;

use Biblioteca\TypesenseBundle\Mapper\Entity\AbstractEntityMapper;
use Biblioteca\TypesenseBundle\Mapper\Mapping\Mapping;
use Biblioteca\TypesenseBundle\Mapper\StandaloneMapperInterface;
use Biblioteca\TypesenseBundle\Tests\Entity\Product;
use Biblioteca\TypesenseBundle\Type\DataTypeEnum;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends AbstractEntityMapper<Product>
 */
class ProductMapper extends AbstractEntityMapper implements StandaloneMapperInterface
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
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

    public function getData(): \Generator
    {
        yield [];
    }

    public function getDataCount(): ?int
    {
        return 0;
    }

    public function transform(object $entity): array
    {
        return [
            'id' => (string) $entity->id,
            'name' => $entity->name,
        ];
    }

    public function getClassName(): string
    {
        return Product::class;
    }
}
