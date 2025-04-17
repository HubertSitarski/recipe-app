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

        $this->ingredientManager->createIngredients($recipe, $data);

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
        
        // Update ingredients only if they've changed
        $this->ingredientManager->updateIngredients($recipe, $data);
        
        return $recipe;
    }

    private function setEntityProperties(Recipe $recipe, RecipeData $data): void
    {
        $recipe->setTitle($data->getTitle());
        $recipe->setInstructions($data->getInstructions());
        $recipe->setCategory($data->getCategory());
        $recipe->setArea($data->getArea());
        $recipe->setThumbnail($data->getThumbnail());
        $recipe->setTags($data->getTags());
        $recipe->setYoutubeUrl($data->getYoutubeUrl());
    }
}
