<?php

namespace App\DTO;

/**
 * Interface for recipe data transfer objects
 */
interface RecipeData
{
    /**
     * Get the external ID of the recipe
     */
    public function getExternalId(): string;

    /**
     * Get the title of the recipe
     */
    public function getTitle(): string;

    /**
     * Get the instructions for the recipe
     */
    public function getInstructions(): string;

    /**
     * Get the category of the recipe
     */
    public function getCategory(): ?string;

    /**
     * Get the area/origin of the recipe
     */
    public function getArea(): ?string;

    /**
     * Get the thumbnail URL of the recipe
     */
    public function getThumbnail(): ?string;

    /**
     * Get the tags for the recipe
     */
    public function getTags(): ?string;

    /**
     * Get the YouTube URL for the recipe
     */
    public function getYoutubeUrl(): ?string;

    /**
     * Get the ingredients with their measurements
     * 
     * @return array<string, string> Array of ingredients with measurements
     */
    public function getIngredients(): array;
} 