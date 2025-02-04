<?php

namespace Biblioverse\TypesenseBundle\Command;

use Biblioverse\TypesenseBundle\CollectionAlias\CollectionAliasInterface;
use Biblioverse\TypesenseBundle\Mapper\CollectionManagerInterface;
use Biblioverse\TypesenseBundle\Mapper\DataGeneratorInterface;
use Biblioverse\TypesenseBundle\Mapper\Locator\MapperLocatorInterface;
use Biblioverse\TypesenseBundle\Populate\PopulateService;
use Biblioverse\TypesenseBundle\Populate\WaitFor\WaitForInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

#[AsCommand(
    name: 'biblioverse:typesense:populate',
    description: 'Populate Typesense collections',
)]
final class TypesensePopulateCommand extends Command
{
    private const NB_RETRY = 10;

    public function __construct(
        private readonly PopulateService $populateService,
        private readonly MapperLocatorInterface $mapperLocator,
        private readonly CollectionAliasInterface $collectionAlias,
        /** @var WaitForInterface[] */
        #[AutowireIterator(tag: WaitForInterface::TAG_NAME)]
        private readonly iterable $waitForServices = [],
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
            )
            ->addOption(
                'nb-retry',
                null,
                InputOption::VALUE_REQUIRED,
                'If set, the command will retry connecting multiple times',
                self::NB_RETRY
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

        $nbRetry = $input->getOption('nb-retry');
        $nbRetry = is_numeric($nbRetry) ? intval($nbRetry) : self::NB_RETRY;
        foreach ($this->waitForServices as $waitForService) {
            $waitForService->wait($nbRetry, function (int $step, int $total, \Throwable $throwable) use ($symfonyStyle, $waitForService) {
                $symfonyStyle->writeln(sprintf('Waiting for %s to be available (step %d/%d)', $waitForService->getName(), $step, $total));
                $symfonyStyle->comment($throwable->getMessage());
            });
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
