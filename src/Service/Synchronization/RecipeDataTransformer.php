<?php

namespace App\Service\Synchronization;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;

/**
 * Transforms data from API into Doctrine entities
 */
class RecipeDataTransformer
{
    /**
     * Transform API data to Recipe entity
     *
     * @param array $data Raw API data
     * @return Recipe
     */
    public function transformToEntity(array $data): Recipe
    {
        $recipe = new Recipe();
        $recipe->setExternalId($data['idMeal']);
        $recipe->setTitle($data['strMeal']);
        $recipe->setInstructions($data['strInstructions']);
        $recipe->setCategory($data['strCategory'] ?? null);
        $recipe->setArea($data['strArea'] ?? null);
        $recipe->setThumbnail($data['strMealThumb'] ?? null);
        $recipe->setTags($data['strTags'] ?? null);
        $recipe->setYoutubeUrl($data['strYoutube'] ?? null);

        // Process ingredients
        $this->processIngredients($recipe, $data);

        return $recipe;
    }

    /**
     * Update an existing recipe entity with new data
     *
     * @param Recipe $recipe The recipe entity to update
     * @param array $data Raw API data
     * @return Recipe Updated recipe
     */
    public function updateEntity(Recipe $recipe, array $data): Recipe
    {
        $recipe->setTitle($data['strMeal']);
        $recipe->setInstructions($data['strInstructions']);
        $recipe->setCategory($data['strCategory'] ?? null);
        $recipe->setArea($data['strArea'] ?? null);
        $recipe->setThumbnail($data['strMealThumb'] ?? null);
        $recipe->setTags($data['strTags'] ?? null);
        $recipe->setYoutubeUrl($data['strYoutube'] ?? null);

        // Remove existing ingredients to replace with new ones
        foreach ($recipe->getIngredients() as $ingredient) {
            $recipe->removeIngredient($ingredient);
        }

        // Process ingredients
        $this->processIngredients($recipe, $data);

        return $recipe;
    }

    /**
     * Process and add ingredients to recipe
     *
     * @param Recipe $recipe Recipe entity
     * @param array $data Raw API data
     */
    private function processIngredients(Recipe $recipe, array $data): void
    {
        // The API has ingredients and measures numbered from 1 to 20
        for ($i = 1; $i <= 20; $i++) {
            $ingredientKey = "strIngredient{$i}";
            $measureKey = "strMeasure{$i}";

            $ingredientName = $data[$ingredientKey] ?? null;
            $measure = $data[$measureKey] ?? null;

            // Skip empty ingredients
            if (empty($ingredientName) || $ingredientName === null || trim($ingredientName) === '') {
                continue;
            }

            $ingredient = new RecipeIngredient();
            $ingredient->setName(trim($ingredientName));
            $ingredient->setMeasure(trim($measure ?? ''));

            $recipe->addIngredient($ingredient);
        }
    }
}
