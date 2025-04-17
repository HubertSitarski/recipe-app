<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\RecipeRepository;
use App\Service\Comment\CommentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
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
        $recipe = $this->recipeRepository->findWithRelations($id);

        if (!$recipe) {
            throw new NotFoundHttpException('Recipe not found');
        }

        $comment = new Comment();

        $form = $this->getCommentForm($request, $comment);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentService->addCommentToRecipe(
                $recipe,
                $comment
            );

            $this->addFlash('success', 'Comment added successfully!');

            return $this->redirectToRoute('app_recipe_show', ['id' => $id]);
        }

        $latestComments = $this->commentService->getLatestComments($id);

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
            'comment_form' => $form,
            'comments' => $latestComments,
        ]);
    }

    private function getCommentForm(Request $request, Comment $comment): FormInterface
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        return $form;
    }

}