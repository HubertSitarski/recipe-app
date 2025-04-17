<?php

namespace App\Command;

use App\Message\SynchronizeLetterRecipesMessage;
use App\Service\Synchronization\RecipeSynchronizer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Scheduler\Attribute\AsPeriodicTask;

#[AsCommand(
    name: 'app:recipes:synchronize',
    description: 'Synchronize recipes from TheMealDB API',
)]
#[AsPeriodicTask('20 seconds', schedule: 'default')]
class SynchronizeRecipesCommand extends Command
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly RecipeSynchronizer $recipeSynchronizer
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->dispatchFullSynchronization($io);

        return Command::SUCCESS;
    }

    private function dispatchFullSynchronization(SymfonyStyle $io): void
    {
        $io->note('Dispatching full recipe synchronization...');

        try {
            $this->recipeSynchronizer->dispatchSynchronizeForAllLetters();
            $io->success('Full synchronization dispatched successfully. Check the messenger queue status for progress.');
            $io->info('You can run "php bin/console messenger:consume async" to process the queue.');
        } catch (\Exception $e) {
            $this->logger->error('Failed to dispatch synchronization: ' . $e->getMessage());
            $io->error('Failed to dispatch synchronization: ' . $e->getMessage());
        }
    }
}
