<?php

namespace Biblioteca\TypesenseBundle\Tests\Mapper;

use Biblioteca\TypesenseBundle\Mapper\Entity\Identifier\EntityIdentifier;
use Biblioteca\TypesenseBundle\Tests\Entity\Product;
use Biblioteca\TypesenseBundle\Tests\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class EntityIdentifierTest extends KernelTestCase
{
    public const PRODUCT_GET_IDENTIFIER = 'Product GetIdentifier';

    public function testGetIdentifiersValue(): void
    {
        $this->bootKernel();

        $product = new Product();
        $product->name = self::PRODUCT_GET_IDENTIFIER;
        $em = $this->get(EntityManagerInterface::class);
        $em->persist($product);
        $em->flush();

        $identifier = $this->get(EntityIdentifier::class);

        $this->assertSame(['id' => (string) $product->id], $identifier->getIdentifiersValue($product));
    }

    public function tearDown(): void
    {
        $em = $this->get(EntityManagerInterface::class);
        $em->getRepository(Product::class)->createQueryBuilder('p')
            ->delete()
            ->where('p.name = :name')
            ->getQuery()
            ->setParameter('name', self::PRODUCT_GET_IDENTIFIER)
            ->execute();
        parent::tearDown();
    }
}
