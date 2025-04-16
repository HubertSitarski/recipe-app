<?php

namespace App\MessageHandler;

use App\Message\SynchronizeLetterRecipesMessage;
use App\Service\Synchronization\RecipeSynchronizer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handler for processing synchronization of recipes for a specific letter
 */
#[AsMessageHandler]
final class SynchronizeLetterRecipesHandler
{
    public function __construct(
        private readonly RecipeSynchronizer $recipeSynchronizer,
        private readonly LoggerInterface $logger
    ) {}

    public function __invoke(SynchronizeLetterRecipesMessage $message): void
    {
        $letter = $message->letter;

        $this->logger->info("Processing synchronization for letter: {$letter}");

        try {
            $count = $this->recipeSynchronizer->synchronizeByLetter($letter);
            $this->logger->info("Successfully synchronized {$count} recipes for letter {$letter}");
        } catch (\Exception $e) {
            $this->logger->error("Error during synchronization for letter {$letter}: " . $e->getMessage());
            throw $e;
        }
    }
}
