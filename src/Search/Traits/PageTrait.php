<?php

namespace Biblioteca\TypesenseBundle\Search\Traits;

trait PageTrait
{
    public function getPage(): ?int
    {
        return $this->getInt('page');
    }

    public function getTotalPage(): ?int
    {
        return $this->getInt('total_pages');
    }

    public function perPage(): ?int
    {
        return $this->getInt('per_page');
    }

    private function getInt(string $name): ?int
    {
        if (!$this->offsetExists($name) || !is_int($this->data[$name])) {
            return null;
        }

        return $this->data[$name];
    }
}
