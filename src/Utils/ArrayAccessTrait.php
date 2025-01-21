<?php

namespace Biblioverse\TypesenseBundle\Utils;

/**
 * @template TKey
 * @template TValue
 *
 * @implements \ArrayAccess<TKey, TValue>
 */
trait ArrayAccessTrait
{
    /**
     * @param array<TKey, TValue> $data
     */
    public function __construct(protected array $data = [])
    {
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * @param TKey $offset
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * @param TKey $offset
     *
     * @return TValue|null
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset] ?? null;
    }

    /**
     * @param TKey $offset
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
    }

    /**
     * @param TKey $offset
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }
}
