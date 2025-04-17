<?php

namespace App\Command;

use App\Message\SynchronizeLetterRecipesMessage;
use App\Message\SynchronizeRecipesMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:recipes:synchronize',
    description: 'Synchronize recipes from TheMealDB API',
)]
class SynchronizeRecipesCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'letter',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Synchronize recipes for a specific letter (a-z)'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $letter = $input->getOption('letter');

        if ($letter !== null) {
            if (!preg_match('/^[a-z]$/', $letter)) {
                $io->error('Letter must be a single lowercase character (a-z)');
                return Command::FAILURE;
            }

            $this->dispatchSynchronizationForLetter($letter, $io);
        } else {
            $this->dispatchFullSynchronization($io);
        }

        return Command::SUCCESS;
    }

    private function dispatchFullSynchronization(SymfonyStyle $io): void
    {
        $io->note('Dispatching full recipe synchronization...');

        try {
            $this->messageBus->dispatch(new SynchronizeRecipesMessage());
            $io->success('Full synchronization dispatched successfully. Check the messenger queue status for progress.');
            $io->info('You can run "php bin/console messenger:consume async" to process the queue.');
        } catch (\Exception $e) {
            $this->logger->error('Failed to dispatch synchronization: ' . $e->getMessage());
            $io->error('Failed to dispatch synchronization: ' . $e->getMessage());
        }
    }

    private function dispatchSynchronizationForLetter(string $letter, SymfonyStyle $io): void
    {
        $io->note(sprintf('Dispatching synchronization for letter "%s"...', $letter));

        try {
            $this->messageBus->dispatch(new SynchronizeLetterRecipesMessage($letter));
            $io->success(sprintf('Synchronization for letter "%s" dispatched successfully', $letter));
            $io->info('You can run "php bin/console messenger:consume async" to process the queue.');
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Failed to dispatch synchronization for letter "%s": %s', $letter, $e->getMessage()));
            $io->error(sprintf('Failed to dispatch synchronization for letter "%s": %s', $letter, $e->getMessage()));
        }
    }
}
