<?php

namespace App\DTO;

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

    public function getIngredients(): array;
} 