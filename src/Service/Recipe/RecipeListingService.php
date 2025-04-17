<?php

namespace App\Service\Recipe;

use App\Repository\RecipeRepository;

/**
 * Service for retrieving recipe listings with pagination and search
 */
class RecipeListingService
{
    public function __construct(
        private readonly RecipeRepository $recipeRepository
    ) {}

    /**
     * Get paginated list of recipes with optional search
     *
     * @param string|null $searchTerm Search term for recipe title
     * @param int $page Current page number
     * @param int $limit Items per page
     * @return array{recipes: array, total: int, lastPage: int}
     */
    public function getRecipes(?string $searchTerm = null, int $page = 1, int $limit = 10): array
    {
        if ($limit === 0) {
            $limit = 10;
        }

        // Normalize search term
        $searchTerm = $searchTerm ? trim($searchTerm) : null;

        $result = $this->recipeRepository->findByTitlePaginated(
            $searchTerm,
            $page,
            $limit
        );

        // Calculate pagination data
        $totalRecipes = $result['total'];
        $lastPage = max(1, ceil($totalRecipes / $limit));

        return [
            'recipes' => $result['recipes'],
            'total' => $totalRecipes,
            'lastPage' => $lastPage,
        ];
    }
}
