<?php

namespace Biblioteca\TypesenseBundle\EventSubscriber;

use Biblioteca\TypesenseBundle\Mapper\Locator\MapperLocatorInterface;
use Biblioteca\TypesenseBundle\Populate\PopulateService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::onFlush, priority: 500)]
#[AsDoctrineListener(event: Events::postFlush, priority: 500)]
class IndexCollectionSubscriber implements EventSubscriber
{
    /** @var array{'update': object[], 'delete': object[]} */
    private array $map = ['update' => [], 'delete' => []];

    public function __construct(private readonly PopulateService $populateService, private readonly MapperLocatorInterface $mapperLocator)
    {
    }

    public function postFlush(PostFlushEventArgs $postFlushEventArgs): void
    {
        // On post Flush the entities are filled with the Ids (not on flush)
        foreach ($this->map['update'] as $entity) {
            $this->indexEntity($entity);
        }
        foreach ($this->map['delete'] as $entity) {
            $this->removeEntity($entity);
        }

        $this->map = ['update' => [], 'delete' => []];
    }

    public function onFlush(OnFlushEventArgs $onFlushEventArgs): void
    {
        // TODO Only supported entities should be handled
        $unitOfWork = $onFlushEventArgs->getObjectManager()->getUnitOfWork();
        foreach ($unitOfWork->getScheduledEntityDeletions() as $entity) {
            $this->map['delete'][] = $entity;
        }
        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            $this->map['update'][] = $entity;
        }
        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            $this->map['update'][] = $entity;
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

    /**
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
            Events::postFlush,
        ];
    }
}
