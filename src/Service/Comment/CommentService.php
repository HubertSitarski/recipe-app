<?php

namespace App\Service\Comment;

use App\Entity\Comment;
use App\Entity\Recipe;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service for managing recipe comments
 */
class CommentService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CommentRepository $commentRepository
    ) {}

    /**
     * Add a new comment to a recipe
     *
     * @param Recipe $recipe The recipe to comment on
     * @param string $content Comment content
     * @return Comment The created comment
     */
    public function addComment(Recipe $recipe, string $content): Comment
    {
        $comment = new Comment();
        $comment->setRecipe($recipe);
        $comment->setContent($content);

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $comment;
    }

    /**
     * Get latest comments for a recipe
     *
     * @param Recipe|int $recipe Recipe entity or ID
     * @param int $limit Maximum number of comments to retrieve
     * @return Comment[] Array of comments
     */
    public function getLatestComments(Recipe|int $recipe, int $limit = 20): array
    {
        $recipeId = $recipe instanceof Recipe ? $recipe->getId() : $recipe;

        return $this->commentRepository->findLatestByRecipe($recipeId, $limit);
    }
}
