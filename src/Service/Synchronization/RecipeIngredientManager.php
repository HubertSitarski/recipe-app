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
    public function processIngredients(Recipe $recipe, RecipeData $data): void
    {
        foreach ($data->getIngredients() as $ingredientName => $measure) {
            $ingredient = new RecipeIngredient();
            $ingredient->setName(trim($ingredientName));
            $ingredient->setMeasure(trim($measure ?? ''));

            $recipe->addIngredient($ingredient);
        }
    }
}
