<?php

namespace App\MessageHandler;

use App\Message\SynchronizeRecipesMessage;
use App\Service\Synchronization\RecipeSynchronizer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler for processing synchronization of all recipes
 */
#[AsMessageHandler]
final class SynchronizeRecipesHandler
{
    public function __construct(
        private readonly RecipeSynchronizer $recipeSynchronizer,
        private readonly LoggerInterface $logger
    ) {}

    public function __invoke(SynchronizeRecipesMessage $message): void
    {
        $this->logger->info('Starting full recipe synchronization');

        try {
            $this->recipeSynchronizer->dispatchSynchronizeForAllLetters();
            $this->logger->info('Full recipe synchronization dispatched successfully');
        } catch (\Exception $e) {
            $this->logger->error('Error during full recipe synchronization: ' . $e->getMessage());
            throw $e;
        }
    }
}
