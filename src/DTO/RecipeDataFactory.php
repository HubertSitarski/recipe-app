<?php

namespace App\DTO;

/**
 * Factory for creating RecipeData DTOs
 */
class RecipeDataFactory
{
    /**
     * Create a RecipeData DTO from TheMealDB API data
     *
     * @param array $data Raw data from TheMealDB API
     * @return RecipeData
     */
    public function createFromTheMealDb(array $data): RecipeData
    {
        return new TheMealDbRecipeData($data);
    }
    
    /**
     * Create appropriate RecipeData instance based on source
     *
     * @param array $data Raw recipe data
     * @param string $source Source of the data (e.g., 'themealdb')
     * @return RecipeData
     * @throws \InvalidArgumentException If source is not supported
     */
    public function createFromSource(array $data, string $source): RecipeData
    {
        return match (strtolower($source)) {
            'themealdb' => $this->createFromTheMealDb($data),
            default => throw new \InvalidArgumentException("Unsupported recipe data source: {$source}")
        };
    }
} 