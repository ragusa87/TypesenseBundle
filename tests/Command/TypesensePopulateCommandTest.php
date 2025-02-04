<?php

namespace Biblioverse\TypesenseBundle\Tests\Command;

use Biblioverse\TypesenseBundle\CollectionAlias\CollectionAliasInterface;
use Biblioverse\TypesenseBundle\Command\TypesensePopulateCommand;
use Biblioverse\TypesenseBundle\Mapper\CollectionManagerInterface;
use Biblioverse\TypesenseBundle\Mapper\DataGeneratorInterface;
use Biblioverse\TypesenseBundle\Mapper\Entity\EntityTransformerInterface;
use Biblioverse\TypesenseBundle\Mapper\Locator\MapperLocatorInterface;
use Biblioverse\TypesenseBundle\Populate\PopulateService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class TypesensePopulateCommandTest extends TestCase
{
    public function testEmpty(): void
    {
        $typesensePopulateCommand = new TypesensePopulateCommand($this->createMock(PopulateService::class), $this->createMock(MapperLocatorInterface::class), $this->createMock(CollectionAliasInterface::class));
        $commandTester = new CommandTester($typesensePopulateCommand);
        $code = $commandTester->execute([]);
        $this->assertSame(0, $code);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('No data generator found', $output);
    }

    public function testOnceCollection(): void
    {
        $mapper = $this->createMock(MapperLocatorInterface::class);
        $mapper->method('countDataGenerator')->willReturn(1);
        $mapper->method('getDataGenerator')->willReturn($this->createMock(DataGeneratorInterface::class));
        $mapper->method('getEntityTransformers')->willReturnCallback(fn () => ['mapper' => $this->createMock(EntityTransformerInterface::class)]);
        $mapper->method('getMappers')->willReturnCallback(fn () => ['mapper' => $this->createMock(CollectionManagerInterface::class)]);
        $typesensePopulateCommand = new TypesensePopulateCommand($this->createMock(PopulateService::class), $mapper, $this->createMock(CollectionAliasInterface::class));
        $commandTester = new CommandTester($typesensePopulateCommand);
        $code = $commandTester->execute([]);
        $this->assertSame(0, $code);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Creating collection', $output);
    }

    public function testNoAlias(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Error while populating collection');

        $mapper = $this->createMock(MapperLocatorInterface::class);
        $mapper->method('countDataGenerator')->willReturn(1);
        $mapper->method('getDataGenerator')->willReturn($this->createMock(DataGeneratorInterface::class));
        $mapper->method('getEntityTransformers')->willReturnCallback(fn () => ['mapper' => $this->createMock(EntityTransformerInterface::class)]);
        $mapper->method('getMappers')->willReturnCallback(fn () => ['mapper' => $this->createMock(CollectionManagerInterface::class)]);

        $populate = $this->createMock(PopulateService::class);
        $populate->method('fillCollection')->willThrowException(new \RuntimeException('Error while populating collection'));

        $typesensePopulateCommand = new TypesensePopulateCommand($populate, $mapper, $this->createMock(CollectionAliasInterface::class));

        $commandTester = null;
        try {
            $commandTester = new CommandTester($typesensePopulateCommand);
            $code = $commandTester->execute([]);
        } finally {
            $output = $commandTester?->getDisplay() ?? '';
            $this->assertStringContainsString('Error while populating collection', $output);
            $this->assertStringNotContainsString('Aliasing collection', $output);
        }

        $this->assertSame(0, $code);
    }
}
