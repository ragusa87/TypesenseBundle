<?php

namespace Biblioteca\TypesenseBundle\Command;

use Biblioteca\TypesenseBundle\CollectionName\AliasName;
use Biblioteca\TypesenseBundle\Mapper\Locator\MapperLocatorInterface;
use Biblioteca\TypesenseBundle\Mapper\MapperInterface;
use Biblioteca\TypesenseBundle\Populate\PopulateService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'biblioteca:typesense:populate',
    description: 'Populate Typesense collections',
)]
class TypesensePopulateCommand extends Command
{
    public function __construct(
        private readonly PopulateService $populateService,
        private readonly MapperLocatorInterface $mapperLocator,
        private readonly AliasName $aliasName,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $count = $this->mapperLocator->count();
        if ($count === 0) {
            $io->warning('No mappers found. Declare at least one service implementing '.MapperInterface::class);

            return Command::SUCCESS;
        }

        $progress = new ProgressBar($output, 0);

        foreach ($this->mapperLocator->getMappers() as $shortName => $mapper) {
            $longName = $this->aliasName->getName($shortName);

            $io->writeln('Creating collection '.$longName);
            $this->populateService->createCollection($longName, $mapper);

            try {
                $io->writeln('Filling collection '.$longName);
                $progress->start($mapper->getDataCount());
                $progress->display();
                foreach ($this->populateService->fillCollection($longName, $mapper) as $_) {
                    $progress->advance();
                }
                $progress->finish();
            } catch (\Exception $e) {
                $io->error($e->getMessage());
                $this->populateService->deleteCollection($longName);
            } finally {
                $progress->clear();
            }

            if ($this->aliasName->isAliasEnabled()) {
                $io->writeln(sprintf('Aliasing collection %s to <info>%s</info>', $longName, $shortName));
                $this->aliasName->switch($shortName, $longName);
            }
        }

        $io->success('Finished');

        return Command::SUCCESS;
    }
}
