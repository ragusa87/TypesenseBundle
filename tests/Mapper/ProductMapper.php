<?php

namespace Biblioteca\TypesenseBundle\Tests\Mapper;

use Biblioteca\TypesenseBundle\Mapper\AbstractEntityMapper;
use Biblioteca\TypesenseBundle\Mapper\Mapping\Mapping;
use Biblioteca\TypesenseBundle\Tests\Entity\Product;
use Biblioteca\TypesenseBundle\Tests\Repository\ProductRepository;
use Biblioteca\TypesenseBundle\Type\DataTypeEnum;

/**
 * @extends AbstractEntityMapper<Product>
 */
class ProductMapper extends AbstractEntityMapper
{
    public function __construct(ProductRepository $entityRepository)
    {
        parent::__construct($entityRepository);
    }

    public static function getName(): string
    {
        return 'products';
    }

    public function getMapping(): Mapping
    {
        return (new Mapping())
            ->add(name: 'id', type: DataTypeEnum::STRING);
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
}
