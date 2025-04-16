<?php

namespace App\Service\Synchronization;

use App\Entity\Recipe;
use App\Provider\MealProviderInterface;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\SynchronizeLetterRecipesMessage;

/**
 * Service responsible for synchronizing recipes from provider to database
 */
class RecipeSynchronizer
{
    public function __construct(
        private readonly MealProviderInterface $mealProvider,
        private readonly EntityManagerInterface $entityManager,
        private readonly RecipeRepository $recipeRepository,
        private readonly RecipeDataTransformer $recipeDataTransformer,
        private readonly MessageBusInterface $messageBus,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Dispatch synchronization messages for all alphabet letters
     */
    public function dispatchSynchronizeForAllLetters(): void
    {
        // Dispatch synchronization for each letter of the alphabet
        foreach (range('a', 'z') as $letter) {
            $this->messageBus->dispatch(new SynchronizeLetterRecipesMessage($letter));
        }

        $this->logger->info('Dispatched synchronization messages for all alphabet letters');
    }

    /**
     * Synchronize recipes starting with a specific letter
     *
     * @param string $letter The letter to synchronize
     * @return int Number of recipes synchronized
     */
    public function synchronizeByLetter(string $letter): int
    {
        $this->logger->info("Starting synchronization for letter: {$letter}");

        $recipesData = $this->mealProvider->fetchByLetter($letter);

        if (empty($recipesData)) {
            $this->logger->info("No recipes found for letter: {$letter}");
            return 0;
        }

        $count = 0;

        foreach ($recipesData as $recipeData) {
            try {
                $this->processRecipe($recipeData);
                $count++;
            } catch (\Exception $e) {
                $this->logger->error('Error synchronizing recipe: ' . $e->getMessage(), [
                    'recipe_id' => $recipeData['idMeal'] ?? 'unknown',
                    'recipe_name' => $recipeData['strMeal'] ?? 'unknown',
                ]);
            }
        }

        $this->logger->info("Synchronized {$count} recipes for letter: {$letter}");

        return $count;
    }

    /**
     * Process a single recipe - create or update
     *
     * @param array $recipeData Recipe data from API
     * @return Recipe The processed recipe entity
     */
    private function processRecipe(array $recipeData): Recipe
    {
        // Check if recipe already exists
        $recipe = $this->recipeRepository->findOneBy(['externalId' => $recipeData['idMeal']]);

        if ($recipe) {
            // Update existing recipe
            $recipe = $this->recipeDataTransformer->updateEntity($recipe, $recipeData);
            $this->logger->debug('Updated recipe: ' . $recipe->getTitle());
        } else {
            // Create new recipe
            $recipe = $this->recipeDataTransformer->transformToEntity($recipeData);
            $this->entityManager->persist($recipe);
            $this->logger->debug('Created new recipe: ' . $recipe->getTitle());
        }

        $this->entityManager->flush();

        return $recipe;
    }
}
