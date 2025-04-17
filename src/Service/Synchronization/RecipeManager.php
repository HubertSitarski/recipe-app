<?php

namespace App\Service\Synchronization;

use App\DTO\RecipeData;
use App\Entity\Recipe;

class RecipeManager
{
    public function __construct(
        private readonly RecipeIngredientManager $ingredientManager
    ) {
    }

    public function createEntity(RecipeData $data): Recipe
    {
        $recipe = new Recipe();
        $recipe->setExternalId($data->getExternalId());

        $this->setEntityProperties($recipe, $data);

        $this->ingredientManager->createIngredients($recipe, $data);

        return $recipe;
    }

    public function updateEntity(Recipe $recipe, RecipeData $data): Recipe
    {
        $this->setEntityProperties($recipe, $data);
        
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
