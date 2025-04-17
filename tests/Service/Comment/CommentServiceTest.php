<?php

namespace App\Tests\Service\Comment;

use App\Entity\Comment;
use App\Entity\Recipe;
use App\Repository\CommentRepository;
use App\Service\Comment\CommentService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CommentServiceTest extends TestCase
{
    private CommentService $commentService;
    private MockObject|EntityManagerInterface $entityManager;
    private MockObject|CommentRepository $commentRepository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->commentRepository = $this->createMock(CommentRepository::class);
        $this->commentService = new CommentService($this->entityManager, $this->commentRepository);
    }

    public function testAddCommentToRecipe(): void
    {
        $recipe = $this->createMock(Recipe::class);
        $recipe->method('getId')->willReturn(42);

        $comment = $this->createMock(Comment::class);
        $comment->expects($this->once())
            ->method('setRecipe')
            ->with($this->identicalTo($recipe))
            ->willReturnSelf();

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->identicalTo($comment));
        
        $this->entityManager->expects($this->once())
            ->method('flush');

        $result = $this->commentService->addCommentToRecipe($recipe, $comment);

        $this->assertSame($comment, $result);
    }

    public function testGetLatestCommentsWithRecipeEntity(): void
    {
        $recipe = $this->createMock(Recipe::class);
        $recipe->method('getId')->willReturn(42);
        $limit = 10;
        
        $expectedComments = $this->createCommentArray(3);
        
        $this->commentRepository->expects($this->once())
            ->method('findLatestByRecipe')
            ->with(42, $limit)
            ->willReturn($expectedComments);
            
        $result = $this->commentService->getLatestComments($recipe, $limit);
        
        $this->assertSame($expectedComments, $result);
        $this->assertCount(3, $result);
    }
    
    public function testGetLatestCommentsWithRecipeId(): void
    {
        $recipeId = 42;
        $limit = 5;
        
        $expectedComments = $this->createCommentArray(2);
        
        $this->commentRepository->expects($this->once())
            ->method('findLatestByRecipe')
            ->with($recipeId, $limit)
            ->willReturn($expectedComments);
            
        $result = $this->commentService->getLatestComments($recipeId, $limit);
        
        $this->assertSame($expectedComments, $result);
        $this->assertCount(2, $result);
    }
    
    public function testGetLatestCommentsWithDefaultLimit(): void
    {
        $recipeId = 42;
        $defaultLimit = 20;
        
        $expectedComments = $this->createCommentArray(5);
        
        $this->commentRepository->expects($this->once())
            ->method('findLatestByRecipe')
            ->with($recipeId, $defaultLimit)
            ->willReturn($expectedComments);
            
        $result = $this->commentService->getLatestComments($recipeId);
        
        $this->assertSame($expectedComments, $result);
    }
    
    public function testGetLatestCommentsWithNoResults(): void
    {
        $recipeId = 999;
        
        $this->commentRepository->expects($this->once())
            ->method('findLatestByRecipe')
            ->willReturn([]);
            
        $result = $this->commentService->getLatestComments($recipeId);
        
        $this->assertEmpty($result);
        $this->assertIsArray($result);
    }

    /**
     * Helper method to create an array of mock Comment objects
     */
    private function createCommentArray(int $count): array
    {
        $comments = [];
        
        for ($i = 0; $i < $count; $i++) {
            $comment = $this->createMock(Comment::class);
            $comment->method('getId')->willReturn($i + 1);
            $comment->method('getContent')->willReturn('Comment content ' . ($i + 1));
            $comment->method('getCreatedAt')->willReturn(new \DateTimeImmutable());
            
            $comments[] = $comment;
        }
        
        return $comments;
    }
} 