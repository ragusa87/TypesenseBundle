<?php

namespace Biblioteca\TypesenseBundle\Tests\DataFixtures;

use Biblioteca\TypesenseBundle\Tests\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public const MAX = 20;

    public function load(ObjectManager $manager): void
    {
        // create 20 Product! Bam!
        foreach (range(1, self::MAX) as $i) {
            $product = new Product();
            $product->name = 'Product '.$i;
            $manager->persist($product);
        }

        $manager->flush();
    }
}
