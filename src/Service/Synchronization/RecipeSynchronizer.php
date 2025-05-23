<?php

namespace App\Service\Synchronization;

use App\DTO\RecipeData;
use App\DTO\RecipeDataFactory;
use App\Entity\Recipe;
use App\Provider\MealProviderInterface;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\SynchronizeLetterRecipesMessage;

class RecipeSynchronizer
{
    public function __construct(
        private readonly MealProviderInterface $mealProvider,
        private readonly EntityManagerInterface $entityManager,
        private readonly RecipeRepository $recipeRepository,
        private readonly RecipeManager $recipeManager,
        private readonly MessageBusInterface $messageBus,
        private readonly RecipeDataFactory $recipeDataFactory,
        private readonly LoggerInterface $logger
    ) {}

    public function dispatchSynchronizeForAllLetters(): void
    {
        foreach (range('a', 'z') as $letter) {
            $this->messageBus->dispatch(new SynchronizeLetterRecipesMessage($letter));
        }

        $this->logger->info('Dispatched synchronization messages for all alphabet letters');
    }

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
            $recipeDataDTO = $this->recipeDataFactory->createFromSource($recipeData, $this->mealProvider->getSource());

            try {
                $this->processRecipe($recipeDataDTO);
                $count++;
            } catch (\Exception $e) {
                $this->logger->error('Error synchronizing recipe: ' . $e->getMessage(), [
                    'recipe_id' => $recipeDataDTO->getExternalId() ?? 'unknown',
                    'recipe_name' => $recipeDataDTO->getTitle() ?? 'unknown',
                ]);
            }
        }

        $this->logger->info("Synchronized {$count} recipes for letter: {$letter}");

        return $count;
    }

    private function processRecipe(RecipeData $recipeData): Recipe
    {
        $recipe = $this->recipeRepository->findOneBy(['externalId' => $recipeData->getExternalId()]);

        if ($recipe) {
            $recipe = $this->recipeManager->updateEntity($recipe, $recipeData);
            $this->logger->debug('Updated recipe: ' . $recipe->getTitle());
        } else {
            $recipe = $this->recipeManager->createEntity($recipeData);
            $this->entityManager->persist($recipe);
            $this->logger->debug('Created new recipe: ' . $recipe->getTitle());
        }

        $this->entityManager->flush();

        return $recipe;
    }
}
