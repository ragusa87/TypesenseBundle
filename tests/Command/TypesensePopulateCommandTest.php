<?php

namespace Biblioteca\TypesenseBundle\Tests\Command;

use Biblioteca\TypesenseBundle\CollectionAlias\CollectionAliasInterface;
use Biblioteca\TypesenseBundle\Command\TypesensePopulateCommand;
use Biblioteca\TypesenseBundle\Mapper\CollectionManagerInterface;
use Biblioteca\TypesenseBundle\Mapper\DataGeneratorInterface;
use Biblioteca\TypesenseBundle\Mapper\Entity\EntityTransformerInterface;
use Biblioteca\TypesenseBundle\Mapper\Locator\MapperLocatorInterface;
use Biblioteca\TypesenseBundle\Populate\PopulateService;
use Symfony\Component\Console\Tester\CommandTester;

class TypesensePopulateCommandTest extends \PHPUnit\Framework\TestCase
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
}
