<?php

namespace App\Service\Synchronization;

use App\DTO\RecipeData;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;

/**
 * Transforms data from API into Doctrine entities
 */
class RecipeManager
{
    public function __construct(
        private readonly RecipeIngredientManager $ingredientManager
    ) {
    }

    /**
     * Transform API data to Recipe entity
     *
     * @param RecipeData $data API data converted to DTO
     * @return Recipe
     */
    public function createEntity(RecipeData $data): Recipe
    {
        $recipe = new Recipe();
        $recipe->setExternalId($data->getExternalId());

        $this->setEntityProperties($recipe, $data);

        $this->ingredientManager->processIngredients($recipe, $data);

        return $recipe;
    }

    /**
     * Update an existing recipe entity with new data
     *
     * @param Recipe $recipe The recipe entity to update
     * @param RecipeData $data API data converted to DTO
     * @return Recipe Updated recipe
     */
    public function updateEntity(Recipe $recipe, RecipeData $data): Recipe
    {
        $this->setEntityProperties($recipe, $data);

        // Remove existing ingredients to replace with new ones
        foreach ($recipe->getIngredients() as $ingredient) {
            $recipe->removeIngredient($ingredient);
        }

        $this->ingredientManager->processIngredients($recipe, $data);

        return $recipe;
    }

    private function setEntityProperties(Recipe $recipe, RecipeData $data): void
    {
        $recipe->setTitle($data->getTitle());
        $recipe->setInstructions($data->getInstructions());
        $recipe->setCategory($data->getCategory() ?? null);
        $recipe->setArea($data->getArea() ?? null);
        $recipe->setThumbnail($data->getThumbnail() ?? null);
        $recipe->setTags($data->getTags() ?? null);
        $recipe->setYoutubeUrl($data->getYoutubeUrl() ?? null);
    }
}
