<?php

namespace Biblioteca\TypesenseBundle\Mapper\Entity;

use Biblioteca\TypesenseBundle\Mapper\CollectionManagerInterface;

/**
 * @template T of object
 *
 * @extends EntityTransformerInterface<T>
 */
interface EntityCollectionManagerInterface extends CollectionManagerInterface, EntityTransformerInterface
{
}
