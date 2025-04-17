<?php

namespace App\Service\Synchronization;

use App\DTO\RecipeData;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;

class RecipeIngredientManager
{
    /**
     * Process and add ingredients to recipe
     *
     * @param Recipe $recipe Recipe entity
     * @param RecipeData $data API data converted to DTO
     */
    public function createIngredients(Recipe $recipe, RecipeData $data): void
    {
        foreach ($data->getIngredients() as $ingredientName => $measure) {
            $ingredient = new RecipeIngredient();
            $ingredient->setName(trim($ingredientName));
            $ingredient->setMeasure(trim($measure ?? ''));

            $recipe->addIngredient($ingredient);
        }
    }

    /**
     * Update recipe ingredients only if they've changed
     *
     * @param Recipe $recipe The recipe entity to update
     * @param RecipeData $data API data with new ingredients
     */
    public function updateIngredients(Recipe $recipe, RecipeData $data): void
    {
        $existingIngredients = $this->createIngredientsMap($recipe);
        $processedIngredients = $this->processNewIngredients($recipe, $data->getIngredients(), $existingIngredients);
        $this->removeObsoleteIngredients($recipe, $existingIngredients, $processedIngredients);
    }

    /**
     * Creates a map of existing ingredients for easy lookup
     *
     * @param Recipe $recipe The recipe entity
     * @return array Map of ingredient name => ingredient entity
     */
    private function createIngredientsMap(Recipe $recipe): array
    {
        $existingIngredients = [];
        foreach ($recipe->getIngredients() as $ingredient) {
            $key = $ingredient->getName();
            $existingIngredients[$key] = $ingredient;
        }
        return $existingIngredients;
    }

    /**
     * Process ingredients from new data - update existing or add new ones
     *
     * @param Recipe $recipe The recipe entity to update
     * @param array $newIngredientsData New ingredient data from API
     * @param array $existingIngredients Map of existing ingredients
     * @return array Map of processed ingredient names
     */
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

    /**
     * Update an existing ingredient if its measure has changed
     *
     * @param RecipeIngredient $ingredient The ingredient to update
     * @param string $measure The new measure value
     */
    private function updateExistingIngredient(RecipeIngredient $ingredient, string $measure): void
    {
        if ($ingredient->getMeasure() !== $measure) {
            $ingredient->setMeasure($measure);
        }
    }

    /**
     * Add a new ingredient to the recipe
     *
     * @param Recipe $recipe The recipe to add to
     * @param string $name Ingredient name
     * @param string $measure Ingredient measure
     */
    private function addNewIngredient(Recipe $recipe, string $name, string $measure): void
    {
        $ingredient = new RecipeIngredient();
        $ingredient->setName($name);
        $ingredient->setMeasure($measure);
        $recipe->addIngredient($ingredient);
    }

    /**
     * Remove ingredients that are no longer in the recipe
     *
     * @param Recipe $recipe The recipe entity
     * @param array $existingIngredients Map of existing ingredients
     * @param array $processedIngredients Map of processed ingredient names
     */
    private function removeObsoleteIngredients(Recipe $recipe, array $existingIngredients, array $processedIngredients): void
    {
        foreach ($existingIngredients as $name => $ingredient) {
            if (!isset($processedIngredients[$name])) {
                $recipe->removeIngredient($ingredient);
            }
        }
    }
}
