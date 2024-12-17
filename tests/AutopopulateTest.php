<?php

use Biblioteca\TypesenseBundle\Populate\PopulateService;
use Biblioteca\TypesenseBundle\Tests\Entity\Product;

class AutopopulateTest extends Biblioteca\TypesenseBundle\Tests\KernelTestCase
{
    private ?int $lastId = null;

    public function setUp(): void
    {
        // TODO Find a clever way to have the last ID
        parent::setUp();
        self::bootKernel();
        $em = $this->get(Doctrine\ORM\EntityManagerInterface::class);
        $p = new Product();
        $p->name = 'lastId';
        $em->persist($p);
        $em->flush();

        $this->lastId = ($p->id ?? 0) + 1;

        $em->getRepository(Product::class)->createQueryBuilder('p')
            ->delete()
            ->where('p.id > 10') // TODO Use fixture count constant
            ->getQuery()
            ->execute();

        self::ensureKernelShutdown();
    }

    public function testAutopopulateNewEntity(): void
    {
        self::bootKernel();

        // Mock the PopulateService to assert 'fillData' is called once
        $populateMock = $this->createMock(PopulateService::class);
        $populateMock->expects($this->once())
        ->method('fillData')
            ->with('products', ['id' => $this->lastId, 'name' => 'test'])
            ->willReturnCallback(function ($name, $data) {
                yield ['id' => (string) $this->lastId, 'name' => 'test'];
            });

        static::getContainer()->set(PopulateService::class, $populateMock);

        $n = new Product();
        $n->name = 'test';

        $em = $this->get(Doctrine\ORM\EntityManagerInterface::class);
        $em->persist($n);
        $em->flush();

        $this->assertTrue(true); // @phpstan-ignore-line
    }
}
