<?php

namespace App\Tests\Repository;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Repository\RecipeIngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecipeIngredientRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private RecipeIngredientRepository $recipeIngredientRepository;
    
    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->recipeIngredientRepository = $this->entityManager->getRepository(RecipeIngredient::class);
        
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
    
    public function testFindByRecipe(): void
    {
        // Arrange
        $recipe = $this->createRecipeWithIngredients();
        
        // Act
        $ingredients = $this->recipeIngredientRepository->findBy(['recipe' => $recipe]);
        
        // Assert
        $this->assertCount(3, $ingredients);
        $ingredientNames = array_map(fn(RecipeIngredient $i) => $i->getName(), $ingredients);
        $this->assertContains('Flour', $ingredientNames);
        $this->assertContains('Sugar', $ingredientNames);
        $this->assertContains('Butter', $ingredientNames);
    }
    
    public function testFindOneByNameAndRecipe(): void
    {
        // Arrange
        $recipe = $this->createRecipeWithIngredients();
        
        // Act
        $ingredient = $this->recipeIngredientRepository->findOneBy([
            'recipe' => $recipe,
            'name' => 'Sugar'
        ]);
        
        // Assert
        $this->assertNotNull($ingredient);
        $this->assertEquals('Sugar', $ingredient->getName());
        $this->assertEquals('100g', $ingredient->getMeasure());
    }
    
    public function testFindAll(): void
    {
        // Arrange
        $this->createRecipeWithIngredients('Recipe 1');
        $this->createRecipeWithIngredients('Recipe 2');
        
        // Act
        $allIngredients = $this->recipeIngredientRepository->findAll();
        
        // Assert
        $this->assertCount(6, $allIngredients); // 3 ingredients for each recipe
    }
    
    /**
     * Helper method to create a recipe with ingredients
     */
    private function createRecipeWithIngredients(string $title = 'Test Recipe'): Recipe
    {
        $recipe = new Recipe();
        $recipe->setExternalId('ext_' . md5($title));
        $recipe->setTitle($title);
        $recipe->setInstructions('Test instructions');
        
        $ingredient1 = new RecipeIngredient();
        $ingredient1->setName('Flour');
        $ingredient1->setMeasure('200g');
        $ingredient1->setRecipe($recipe);
        
        $ingredient2 = new RecipeIngredient();
        $ingredient2->setName('Sugar');
        $ingredient2->setMeasure('100g');
        $ingredient2->setRecipe($recipe);
        
        $ingredient3 = new RecipeIngredient();
        $ingredient3->setName('Butter');
        $ingredient3->setMeasure('50g');
        $ingredient3->setRecipe($recipe);
        
        $this->entityManager->persist($recipe);
        $this->entityManager->persist($ingredient1);
        $this->entityManager->persist($ingredient2);
        $this->entityManager->persist($ingredient3);
        $this->entityManager->flush();
        
        return $recipe;
    }
} 