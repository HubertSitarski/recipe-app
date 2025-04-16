<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\RecipeRepository;
use App\Service\Comment\CommentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RecipeDetailsController extends AbstractController
{
    public function __construct(
        private readonly RecipeRepository $recipeRepository,
        private readonly CommentService $commentService
    ) {}

    #[Route('/recipe/{id}', name: 'app_recipe_show', methods: ['GET', 'POST'])]
    public function show(Request $request, int $id): Response
    {
        // Find recipe with all relations loaded
        $recipe = $this->recipeRepository->findWithRelations($id);

        if (!$recipe) {
            throw new NotFoundHttpException('Recipe not found');
        }

        // Create a new comment
        $comment = new Comment();
        $comment->setRecipe($recipe);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Use comment service to add the comment
            $this->commentService->addComment(
                $recipe,
                $comment->getContent()
            );

            $this->addFlash('success', 'Comment added successfully!');

            // Redirect to the same page to prevent form resubmission
            return $this->redirectToRoute('app_recipe_show', ['id' => $id]);
        }

        // Get the latest comments for this recipe
        $latestComments = $this->commentService->getLatestComments($id);

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
            'comment_form' => $form,
            'comments' => $latestComments,
        ]);
    }
}