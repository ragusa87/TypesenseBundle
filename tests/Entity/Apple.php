<?php

namespace Biblioteca\TypesenseBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class Apple
{
    public function __construct(#[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column(type: \Doctrine\DBAL\Types\Types::INTEGER)]
        public ?int $id = null, ?string $name = null)
    {
        $this->name = $name ?? 'Apple '.$this->id;
    }

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::STRING, length: 255)]
    public string $name;

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
