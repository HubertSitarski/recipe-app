<?php

namespace App\Provider;

/**
 * Interface for meal providers to allow easy swapping
 */
interface MealProviderInterface
{
    public function fetchByLetter(string $letter): array;

    public function fetchById(string $id): array;

    public function getSource(): string;
}
