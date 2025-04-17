<?php

namespace App\DTO;

use App\Enum\MealProviderSource;

class RecipeDataFactory
{
    public function createFromTheMealDb(array $data): RecipeData
    {
        return new TheMealDbRecipeData($data);
    }

    public function createFromSource(array $data, string $source): RecipeData
    {
        return match (strtolower($source)) {
            MealProviderSource::THEMEALDB->value => $this->createFromTheMealDb($data),
            default => throw new \InvalidArgumentException("Unsupported recipe data source: {$source}")
        };
    }
} 