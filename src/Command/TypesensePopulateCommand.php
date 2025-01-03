<?php

namespace Biblioteca\TypesenseBundle\Command;

use Biblioteca\TypesenseBundle\CollectionAlias\CollectionAliasInterface;
use Biblioteca\TypesenseBundle\Mapper\Locator\MapperLocatorInterface;
use Biblioteca\TypesenseBundle\Mapper\MapperInterface;
use Biblioteca\TypesenseBundle\Populate\PopulateService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
        private readonly CollectionAliasInterface $collectionAlias,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'no-data',
                null,
                InputOption::VALUE_NONE,
                'If set, the command will only create indexes without populating data'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);

        $count = $this->mapperLocator->count();
        if ($count === 0) {
            $symfonyStyle->warning('No mappers found. Declare at least one service implementing '.MapperInterface::class);

            return Command::SUCCESS;
        }

        foreach ($this->mapperLocator->getMappers() as $shortName => $mapper) {
            $longName = $this->collectionAlias->getName($shortName);

            $symfonyStyle->writeln('Creating collection '.$longName);
            $this->populateService->createCollection($longName, $mapper);

            if (false === $input->getOption('no-data')) {
                $this->fillCollection($symfonyStyle, $longName, $mapper, $output);
            }

            $symfonyStyle->writeln(sprintf('Aliasing collection %s to <info>%s</info>', $longName, $shortName));
            $this->collectionAlias->switch($shortName, $longName);
        }

        $symfonyStyle->success('Finished');

        return Command::SUCCESS;
    }

    private function fillCollection(SymfonyStyle $symfonyStyle, string $longName, MapperInterface $mapper, OutputInterface $output): void
    {
        $progressBar = new ProgressBar($output, 0);

        try {
            $symfonyStyle->writeln('Filling collection '.$longName);
            $progressBar->start($mapper->getDataCount());
            $progressBar->display();
            foreach ($this->populateService->fillCollection($longName, $mapper) as $_) {
                $progressBar->advance();
            }
            $progressBar->finish();
        } catch (\Exception $e) {
            $symfonyStyle->error($e->getMessage());
            $this->populateService->deleteCollection($longName);
        } finally {
            $progressBar->clear();
        }
    }
}
