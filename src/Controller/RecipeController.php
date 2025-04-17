<?php

namespace App\Controller;

use App\Form\RecipeSearchType;
use App\Service\Recipe\RecipeListingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    public function __construct(
        private readonly RecipeListingService $recipeListingService
    ) {}

    #[Route('/', name: 'app_recipe_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = $request->query->getInt('limit', 10);
        $search = $request->query->get('search', '');

        $form = $this->getSearchForm($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $search = $formData['search'] ?? '';
        }

        $result = $this->recipeListingService->getRecipes(
            $search ?: null,
            $page,
            $limit
        );

        return $this->render('recipe/index.html.twig', [
            'recipes' => $result['recipes'],
            'search_form' => $form,
            'current_search' => $search,
            'current_page' => $page,
            'limit' => $limit,
            'total_recipes' => $result['total'],
            'last_page' => $result['lastPage'],
        ]);
    }

    private function getSearchForm(Request $request): FormInterface
    {
        $form = $this->createForm(RecipeSearchType::class, null, [
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
        $form->handleRequest($request);

        return $form;
    }
}