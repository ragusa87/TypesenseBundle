<?php

namespace Biblioteca\TypesenseBundle\EventSubscriber;

use Biblioteca\TypesenseBundle\Mapper\Entity\Identifier\EntityIdentifierInterface;
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
    /** @var array<string,array{id: string}> Store identity identifiers */
    private array $ids = [];

    public function __construct(
        private readonly PopulateService $populateService,
        private readonly MapperLocatorInterface $mapperLocator,
        private readonly EntityIdentifierInterface $entityIdentifier,
    ) {
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
        $this->ids = [];
    }

    public function onFlush(OnFlushEventArgs $onFlushEventArgs): void
    {
        // TODO Only supported entities should be handled
        $unitOfWork = $onFlushEventArgs->getObjectManager()->getUnitOfWork();
        foreach ($unitOfWork->getScheduledEntityDeletions() as $entity) {
            if (!$this->mapperLocator->hasEntityMappers($entity::class)) {
                continue;
            }
            $this->map['delete'][] = $entity;
            // Entity identifiers are null-ed on delete, so we need to store them before
            $ids = $this->entityIdentifier->getIdentifiersValue($entity);
            $this->ids[spl_object_hash($entity)] = $ids;
        }
        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            if (!$this->mapperLocator->hasEntityMappers($entity::class)) {
                continue;
            }
            $this->map['update'][] = $entity;
        }
        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if (!$this->mapperLocator->hasEntityMappers($entity::class)) {
                continue;
            }
            $this->map['update'][] = $entity;
        }
    }

    private function indexEntity(object $entity): void
    {
        foreach ($this->mapperLocator->getEntityMappers($entity::class) as $name => $entityMapper) {
            if (!$entityMapper->support($entity)) {
                continue;
            }

            $ids = $this->entityIdentifier->getIdentifiersValue($entity);
            $data = $entityMapper->transform($entity);
            $this->populateService->fillData($name, $ids + $data);
        }
    }

    private function removeEntity(object $entity): void
    {
        foreach ($this->mapperLocator->getEntityMappers($entity::class) as $name => $entityMapper) {
            if (!$entityMapper->support($entity)) {
                continue;
            }
            // We inject the entity identifiers to delete them
            /** @var array{'id': string} $ids */
            $ids = $this->ids[spl_object_hash($entity)];
            $this->populateService->deleteData($name, $ids + $entityMapper->transform($entity));
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
