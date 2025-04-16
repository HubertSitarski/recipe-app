<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeDetailsController extends AbstractController
{
    #[Route('/recipe/{id}', name: 'app_recipe_show', methods: ['GET', 'POST'])]
    public function show(Request $request, int $id): Response
    {
        return $this->render('recipe/show.html.twig', [
            'controller_name' => 'RecipeDetailsController',
        ]);
    }
}