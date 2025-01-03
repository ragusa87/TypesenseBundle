<?php

namespace Biblioteca\TypesenseBundle\Indexer;

use Biblioteca\TypesenseBundle\Mapper\Entity\Identifier\EntityIdentifierInterface;
use Biblioteca\TypesenseBundle\Mapper\Locator\MapperLocatorInterface;
use Biblioteca\TypesenseBundle\Populate\PopulateService;

class Indexer implements IndexerInterface
{
    public function __construct(
        private readonly PopulateService $populateService,
        private readonly MapperLocatorInterface $mapperLocator,
        private readonly EntityIdentifierInterface $entityIdentifier,
    ) {
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function indexEntity(object $entity): self
    {
        foreach ($this->mapperLocator->getEntityMappers($entity::class) as $name => $entityMapper) {
            if (!$entityMapper->support($entity)) {
                continue;
            }

            $ids = $this->entityIdentifier->getIdentifiersValue($entity);
            $data = $entityMapper->transform($entity);
            $this->populateService->fillData($name, $ids + $data);
        }

        return $this;
    }

    public function removeEntity(object $entity): self
    {
        foreach ($this->mapperLocator->getEntityMappers($entity::class) as $name => $entityMapper) {
            if (!$entityMapper->support($entity)) {
                continue;
            }

            // We inject the entity identifiers to delete them
            /** @var array{'id': string} $ids */
            $ids = $this->entityIdentifier->getIdentifiersValue($entity);
            $this->populateService->deleteData($name, $ids + $entityMapper->transform($entity));
        }

        return $this;
    }
}
