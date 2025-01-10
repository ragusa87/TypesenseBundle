<?php

namespace Biblioteca\TypesenseBundle\Tests\EventSubscriber;

use Biblioteca\TypesenseBundle\EventSubscriber\IndexCollectionSubscriber;
use Biblioteca\TypesenseBundle\Mapper\Entity\EntityTransformerInterface;
use Biblioteca\TypesenseBundle\Mapper\Entity\Identifier\EntityIdentifierInterface;
use Biblioteca\TypesenseBundle\Mapper\Locator\MapperLocatorInterface;
use Biblioteca\TypesenseBundle\Populate\PopulateService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[CoversClass(IndexCollectionSubscriber::class)]
class IndexCollectionSubscriberTest extends TestCase
{
    /**
     * @param callable(MockObject&PopulateService):void $callable
     *
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function getIndexCollectionSubscriber(?object $entity, ?callable $callable): IndexCollectionSubscriber
    {
        $populateService = $this->createMock(PopulateService::class);
        if (null !== $callable) {
            $callable($populateService);
        }
        $mapperLocator = $this->createMock(MapperLocatorInterface::class);
        $mapperLocator->method('getEntityTransformers')->willReturn(['mapper' => new class implements EntityTransformerInterface {
            public function support($entity): bool
            {
                return true;
            }

            public function transform($entity): array
            {
                return ['data' => 'value'];
            }
        }]);

        $mapperLocator->method('hasEntityTransformer')->willReturn(true);

        $entityIdentifier = $this->createMock(EntityIdentifierInterface::class);
        $entityIdentifier->method('getIdentifiersValue')->willReturn(['id' => '123']);

        return new IndexCollectionSubscriber($populateService, $mapperLocator, $entityIdentifier);
    }

    public function testGetSubscribedEvents(): void
    {
        $this->assertSame(
            ['onFlush', 'postFlush'],
            $this->getIndexCollectionSubscriber(null, null)->getSubscribedEvents()
        );
    }

    public function testOnFlush(): void
    {
        $entity = new \stdClass();

        $unitOfWork = $this->createMock(UnitOfWork::class);
        $unitOfWork->method('getScheduledEntityDeletions')->willReturn([]);
        $unitOfWork->method('getScheduledEntityInsertions')->willReturn([$entity]);
        $unitOfWork->method('getScheduledEntityUpdates')->willReturn([]);

        $objectManager = $this->createMock(EntityManager::class);
        $objectManager->method('getUnitOfWork')->willReturn($unitOfWork);
        $onFlushEventArgs = new OnFlushEventArgs($objectManager);

        $indexCollectionSubscriber = $this->getIndexCollectionSubscriber($entity, function (PopulateService&MockObject $populate) {
            $populate->expects($this->once())->method('fillData')->with('mapper', ['id' => '123', 'data' => 'value']);
        });
        $indexCollectionSubscriber->onFlush($onFlushEventArgs);
        $indexCollectionSubscriber->postFlush(new PostFlushEventArgs($objectManager));
        $this->assertTrue(true); // @phpstan-ignore-line Assertion done via mock
    }
}
