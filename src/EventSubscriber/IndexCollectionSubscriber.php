<?php

namespace Biblioverse\TypesenseBundle\EventSubscriber;

use Biblioverse\TypesenseBundle\Indexer\IndexerInterface;
use Biblioverse\TypesenseBundle\Mapper\Entity\Identifier\EntityIdentifierInterface;
use Biblioverse\TypesenseBundle\Mapper\Locator\MapperLocatorInterface;
use Biblioverse\TypesenseBundle\Populate\PopulateService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::onFlush, priority: 500)]
#[AsDoctrineListener(event: Events::postFlush, priority: 500)]
class IndexCollectionSubscriber implements EventSubscriber, IndexerInterface
{
    /** @var array{'update': object[], 'delete': object[]} */
    private array $map = ['update' => [], 'delete' => []];
    /** @var array<string,array{id: string}> Store identity identifiers */
    private array $ids = [];

    public function __construct(
        private readonly PopulateService $populateService,
        private readonly MapperLocatorInterface $mapperLocator,
        private readonly EntityIdentifierInterface $entityIdentifier,
        private bool $enabled = true,
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
        $unitOfWork = $onFlushEventArgs->getObjectManager()->getUnitOfWork();
        foreach ($unitOfWork->getScheduledEntityDeletions() as $entity) {
            if (!$this->mapperLocator->hasEntityTransformer($entity::class)) {
                continue;
            }
            $this->map['delete'][] = $entity;
            // Entity identifiers are null-ed on delete, so we need to store them before
            $ids = $this->entityIdentifier->getIdentifiersValue($entity);
            $this->ids[spl_object_hash($entity)] = $ids;
        }
        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            if (!$this->mapperLocator->hasEntityTransformer($entity::class)) {
                continue;
            }
            $this->map['update'][] = $entity;
        }
        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if (!$this->mapperLocator->hasEntityTransformer($entity::class)) {
                continue;
            }
            $this->map['update'][] = $entity;
        }
    }

    public function indexEntity(object $entity): self
    {
        foreach ($this->mapperLocator->getEntityTransformers($entity::class) as $name => $entityTransformer) {
            if (!$this->enabled || !$entityTransformer->support($entity)) {
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
            if (!$this->enabled || !$entityTransformer->support($entity)) {
                continue;
            }
            // We inject the entity identifiers to delete them
            /** @var array{'id': string} $ids */
            $ids = $this->ids[spl_object_hash($entity)];
            $this->populateService->deleteData($name, $ids + $entityTransformer->transform($entity));
        }

        return $this;
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

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }
}
