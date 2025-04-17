<?php

namespace App\Tests\Repository;

use App\Entity\Comment;
use App\Entity\Recipe;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CommentRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private CommentRepository $commentRepository;
    
    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->commentRepository = $this->entityManager->getRepository(Comment::class);
        
        // Start transaction for each test
        $this->entityManager->beginTransaction();
    }
    
    protected function tearDown(): void
    {
        // Roll back transaction after each test
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
        }
        
        parent::tearDown();
    }
    
    public function testFindLatestByRecipe(): void
    {
        $recipe = $this->createRecipe('Test Recipe');
        $this->createCommentsForRecipe($recipe, 5);
        
        $comments = $this->commentRepository->findLatestByRecipe($recipe->getId(), 3);
        
        $this->assertCount(3, $comments);
        $this->assertEquals('Comment 5', $comments[0]->getContent());
        $this->assertEquals('Comment 4', $comments[1]->getContent());
        $this->assertEquals('Comment 3', $comments[2]->getContent());
    }
    
    public function testFindLatestByRecipeWithNoComments(): void
    {
        $recipe = $this->createRecipe('Recipe Without Comments');
        
        $comments = $this->commentRepository->findLatestByRecipe($recipe->getId());
        
        $this->assertCount(0, $comments);
        $this->assertEmpty($comments);
    }
    
    public function testFindLatestByRecipeDefaultLimit(): void
    {
        $recipe = $this->createRecipe('Recipe with Many Comments');
        $this->createCommentsForRecipe($recipe, 25); // Create 25 comments
        
        $comments = $this->commentRepository->findLatestByRecipe($recipe->getId());
        
        $this->assertCount(20, $comments);
        $this->assertEquals('Comment 25', $comments[0]->getContent()); // Latest first
    }
    
    /**
     * Helper method to create a recipe
     */
    private function createRecipe(string $title): Recipe
    {
        $recipe = new Recipe();
        $recipe->setExternalId('ext_' . md5($title));
        $recipe->setTitle($title);
        $recipe->setInstructions('Test instructions');
        
        $this->entityManager->persist($recipe);
        $this->entityManager->flush();
        
        return $recipe;
    }
    
    /**
     * Helper method to create comments for a recipe
     */
    private function createCommentsForRecipe(Recipe $recipe, int $count): void
    {
        for ($i = 1; $i <= $count; $i++) {
            $comment = new Comment();
            $comment->setContent("Comment $i");
            $comment->setRecipe($recipe);
            $this->entityManager->persist($comment);
        }
        
        $this->entityManager->flush();
    }
}
