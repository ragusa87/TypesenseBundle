<?php

namespace Biblioverse\TypesenseBundle\Command;

use Biblioverse\TypesenseBundle\CollectionAlias\CollectionAliasInterface;
use Biblioverse\TypesenseBundle\Mapper\CollectionManagerInterface;
use Biblioverse\TypesenseBundle\Mapper\DataGeneratorInterface;
use Biblioverse\TypesenseBundle\Mapper\Locator\MapperLocatorInterface;
use Biblioverse\TypesenseBundle\Populate\PopulateService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'biblioverse:typesense:populate',
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

        $count = $this->mapperLocator->countDataGenerator();
        if ($count === 0) {
            $symfonyStyle->warning('No data generator found. Declare at least one service implementing '.CollectionManagerInterface::class);

            return Command::SUCCESS;
        }

        foreach ($this->mapperLocator->getMappers() as $shortName => $collectionManager) {
            $longName = $this->collectionAlias->getName($shortName);

            $symfonyStyle->writeln('Creating collection '.$longName);
            $this->populateService->createCollection($longName, $collectionManager->getMapping());

            if (false === $input->getOption('no-data')) {
                $dataProvider = $this->mapperLocator->getDataGenerator($shortName);
                $this->fillCollection($symfonyStyle, $longName, $dataProvider, $output);
            }

            $symfonyStyle->writeln(sprintf('Aliasing collection %s to <info>%s</info>', $longName, $shortName));
            $this->collectionAlias->switch($shortName, $longName);
        }

        $symfonyStyle->success('Finished');

        return Command::SUCCESS;
    }

    /**
     * @throws \RuntimeException
     */
    private function fillCollection(SymfonyStyle $symfonyStyle, string $longName, DataGeneratorInterface $dataGenerator, OutputInterface $output): void
    {
        $progressBar = new ProgressBar($output, 0);

        try {
            $symfonyStyle->writeln('Filling collection '.$longName);
            $progressBar->start($dataGenerator->getDataCount());
            $progressBar->display();
            $this->populateService->fillCollection($longName, $dataGenerator, function (array $items) use ($progressBar) {
                $progressBar->advance(count($items));
            });
            $progressBar->finish();
        } catch (\Exception $e) {
            $symfonyStyle->error($e->getMessage());
            $this->populateService->deleteCollection($longName);
            throw new \RuntimeException('Error while populating collection '.$longName, 0, $e);
        } finally {
            $progressBar->clear();
        }
    }
}
