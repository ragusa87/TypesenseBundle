<?php

namespace Biblioteca\TypesenseBundle\EventSubscriber;

use Biblioteca\TypesenseBundle\Mapper\Locator\MapperLocatorInterface;
use Biblioteca\TypesenseBundle\Populate\PopulateService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::onFlush, priority: 500)]
class IndexCollectionSubscriber
{
    public function __construct(
        private readonly PopulateService $populateService,
        private readonly MapperLocatorInterface $mapperLocator,
    ) {
    }

    public function onFlush(OnFlushEventArgs $onFlushEventArgs): void
    {
        $unitOfWork = $onFlushEventArgs->getObjectManager()->getUnitOfWork();
        foreach ($unitOfWork->getScheduledEntityDeletions() as $entity) {
            $this->removeEntity($entity);
        }
        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            $this->indexEntity($entity);
        }
        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            $this->indexEntity($entity);
        }
    }

    private function indexEntity(object $entity): void
    {
        foreach ($this->mapperLocator->getEntityMappers($entity::class) as $entityMapper) {
            if ($entityMapper->support($entity)) {
                $this->populateService->fillData($entityMapper::getName(), $entityMapper->transform($entity));
            }
        }
    }

    private function removeEntity(object $entity): void
    {
        foreach ($this->mapperLocator->getEntityMappers($entity::class) as $entityMapper) {
            if ($entityMapper->support($entity)) {
                $this->populateService->deleteData($entityMapper::getName(), $entityMapper->transform($entity));
            }
        }
    }
}
