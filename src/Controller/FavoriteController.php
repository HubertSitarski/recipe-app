<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FavoriteController extends AbstractController
{
    public function __construct(
        private readonly RecipeRepository $recipeRepository
    ) {}

    #[Route('/favorites', name: 'app_favorite_index', methods: ['GET'])]
    public function index(): Response
    {
        // Get all recipes
        // They will be filtered on the client side using JavaScript
        $recipes = $this->recipeRepository->findAll();

        return $this->render('favorite/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }
}
