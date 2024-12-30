<?php

namespace Biblioteca\TypesenseBundle\Tests\Entity;

use Biblioteca\TypesenseBundle\Tests\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
    public ?int $id = null;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 255)]
    public string $name;

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
