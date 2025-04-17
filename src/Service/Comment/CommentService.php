<?php

namespace App\Service\Comment;

use App\Entity\Comment;
use App\Entity\Recipe;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;

class CommentService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CommentRepository $commentRepository
    ) {}

    public function addCommentToRecipe(Recipe $recipe, Comment $comment): Comment
    {
        $comment->setRecipe($recipe);

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $comment;
    }

    public function getLatestComments(Recipe|int $recipe, int $limit = 20): array
    {
        $recipeId = $recipe instanceof Recipe ? $recipe->getId() : $recipe;

        return $this->commentRepository->findLatestByRecipe($recipeId, $limit);
    }
}
