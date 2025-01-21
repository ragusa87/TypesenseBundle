<?php

namespace Biblioverse\TypesenseBundle\Indexer;

use Biblioverse\TypesenseBundle\Mapper\Entity\Identifier\EntityIdentifierInterface;
use Biblioverse\TypesenseBundle\Mapper\Locator\MapperLocatorInterface;
use Biblioverse\TypesenseBundle\Populate\PopulateService;

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
        foreach ($this->mapperLocator->getEntityTransformers($entity::class) as $name => $entityTransformer) {
            if (!$entityTransformer->support($entity)) {
                continue;
            }
            $ids = $this->entityIdentifier->getIdentifiersValue($entity);
            $data = $entityTransformer->transform($entity);
            $this->populateService->fillData($name, $ids + $data);
        }

        return $this;
    }

    public function removeEntity(object $entity): self
    {
        foreach ($this->mapperLocator->getEntityTransformers($entity::class) as $name => $entityTransformer) {
            if (!$entityTransformer->support($entity)) {
                continue;
            }

            // We inject the entity identifiers to delete them
            /** @var array{'id': string} $ids */
            $ids = $this->entityIdentifier->getIdentifiersValue($entity);
            $this->populateService->deleteData($name, $ids + $entityTransformer->transform($entity));
        }

        return $this;
    }
}
