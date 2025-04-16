<?php

namespace App\Provider;

/**
 * Interface for meal providers to allow easy swapping
 */
interface MealProviderInterface
{
    /**
     * Fetch recipes by first letter
     *
     * @param string $letter Single letter to search for
     * @return array Array of recipe data
     */
    public function fetchByLetter(string $letter): array;

    /**
     * Fetch recipe details by id
     *
     * @param string $id Recipe ID
     * @return array Recipe data or empty array if not found
     */
    public function fetchById(string $id): array;
}
