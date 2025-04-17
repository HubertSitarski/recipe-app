<?php

namespace App\DTO;

/**
 * Interface for recipe data transfer objects
 */
interface RecipeData
{
    public function getExternalId(): string;

    public function getTitle(): string;

    public function getInstructions(): string;

    public function getCategory(): ?string;

    public function getArea(): ?string;

    public function getThumbnail(): ?string;

    public function getTags(): ?string;

    public function getYoutubeUrl(): ?string;

    /**
     * @return array<string, string> Array of ingredients with measurements
     */
    public function getIngredients(): array;
} 