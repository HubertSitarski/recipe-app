<?php

namespace App\Service\Recipe;

use App\Repository\RecipeRepository;

class RecipeListingService
{
    public function __construct(
        private readonly RecipeRepository $recipeRepository
    ) {}

    public function getRecipes(?string $searchTerm = null, int $page = 1, int $limit = 10): array
    {
        $limit = $limit === 0 ? 10 : $limit;

        $searchTerm = $searchTerm ? trim($searchTerm) : null;

        $result = $this->recipeRepository->findByTitlePaginated(
            $searchTerm,
            $page,
            $limit
        );

        $totalRecipes = $result['total'];
        $lastPage = max(1, ceil($totalRecipes / $limit));

        return [
            'recipes' => $result['recipes'],
            'total' => $totalRecipes,
            'lastPage' => $lastPage,
        ];
    }
}
