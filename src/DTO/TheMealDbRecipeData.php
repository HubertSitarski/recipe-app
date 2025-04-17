<?php

namespace App\DTO;

/**
 * Implementation of RecipeData for TheMealDB API
 */
class TheMealDbRecipeData implements RecipeData
{
    private const INGREDIENTS_AMOUNT = 20;

    private array $data;
    private array $ingredients = [];

    /**
     * @param array $data Raw data from TheMealDB API
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->extractIngredients();
    }

    /**
     * Extract ingredients and their measurements from raw data
     */
    private function extractIngredients(): void
    {
        for ($i = 1; $i <= static::INGREDIENTS_AMOUNT; $i++) {
            $ingredient = $this->data['strIngredient' . $i] ?? null;
            $measure = $this->data['strMeasure' . $i] ?? null;

            if ($ingredient && trim($ingredient) !== '' && trim($ingredient) !== 'null') {
                $this->ingredients[$ingredient] = $measure ?? '';
            }
        }
    }

    public function getExternalId(): string
    {
        return $this->data['idMeal'] ?? '';
    }

    public function getTitle(): string
    {
        return $this->data['strMeal'] ?? '';
    }

    public function getInstructions(): string
    {
        return $this->data['strInstructions'] ?? '';
    }

    public function getCategory(): ?string
    {
        return $this->data['strCategory'] ?? null;
    }

    public function getArea(): ?string
    {
        return $this->data['strArea'] ?? null;
    }

    public function getThumbnail(): ?string
    {
        return $this->data['strMealThumb'] ?? null;
    }

    public function getTags(): ?string
    {
        return $this->data['strTags'] ?? null;
    }

    public function getYoutubeUrl(): ?string
    {
        return $this->data['strYoutube'] ?? null;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
    }
}
