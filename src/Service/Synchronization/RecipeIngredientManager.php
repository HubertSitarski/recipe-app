<?php

namespace App\Service\Synchronization;

use App\DTO\RecipeData;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;

class RecipeIngredientManager
{
    public function createIngredients(Recipe $recipe, RecipeData $data): void
    {
        foreach ($data->getIngredients() as $ingredientName => $measure) {
            $ingredient = new RecipeIngredient();
            $ingredient->setName(trim($ingredientName));
            $ingredient->setMeasure(trim($measure ?? ''));

            $recipe->addIngredient($ingredient);
        }
    }

    public function updateIngredients(Recipe $recipe, RecipeData $data): void
    {
        $existingIngredients = $this->createIngredientsMap($recipe);
        $processedIngredients = $this->processNewIngredients($recipe, $data->getIngredients(), $existingIngredients);
        $this->removeObsoleteIngredients($recipe, $existingIngredients, $processedIngredients);
    }

    private function createIngredientsMap(Recipe $recipe): array
    {
        $existingIngredients = [];
        foreach ($recipe->getIngredients() as $ingredient) {
            $key = $ingredient->getName();
            $existingIngredients[$key] = $ingredient;
        }
        return $existingIngredients;
    }

    private function processNewIngredients(Recipe $recipe, array $newIngredientsData, array $existingIngredients): array
    {
        $processedIngredients = [];

        foreach ($newIngredientsData as $name => $measure) {
            $name = trim($name);
            $measure = trim($measure ?? '');

            if (isset($existingIngredients[$name])) {
                $this->updateExistingIngredient($existingIngredients[$name], $measure);
            } else {
                $this->addNewIngredient($recipe, $name, $measure);
            }
            $processedIngredients[$name] = true;
        }

        return $processedIngredients;
    }

    private function updateExistingIngredient(RecipeIngredient $ingredient, string $measure): void
    {
        if ($ingredient->getMeasure() !== $measure) {
            $ingredient->setMeasure($measure);
        }
    }

    private function addNewIngredient(Recipe $recipe, string $name, string $measure): void
    {
        $ingredient = new RecipeIngredient();
        $ingredient->setName($name);
        $ingredient->setMeasure($measure);
        $recipe->addIngredient($ingredient);
    }

    private function removeObsoleteIngredients(Recipe $recipe, array $existingIngredients, array $processedIngredients): void
    {
        foreach ($existingIngredients as $name => $ingredient) {
            if (!isset($processedIngredients[$name])) {
                $recipe->removeIngredient($ingredient);
            }
        }
    }
}
