<?php

namespace App\Tests\Repository;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecipeRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private RecipeRepository $recipeRepository;
    
    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->recipeRepository = $this->entityManager->getRepository(Recipe::class);
        
        $this->entityManager->beginTransaction();
    }
    
    protected function tearDown(): void
    {
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
        }
        
        parent::tearDown();
    }
    
    public function testFindByTitlePaginated(): void
    {
        $this->createRecipes([
            'Pasta Carbonara',
            'Tomato Soup',
            'Chicken Curry',
            'Chocolate Cake',
            'Apple Pie'
        ]);
        
        $result = $this->recipeRepository->findByTitlePaginated('Choc');
        
        $this->assertCount(1, $result['recipes']);
        $this->assertEquals(1, $result['total']);
        $this->assertEquals('Chocolate Cake', $result['recipes'][0]->getTitle());
    }
    
    public function testFindByTitlePaginatedWithNoFilter(): void
    {
        $recipeNames = [
            'Pasta Carbonara',
            'Tomato Soup',
            'Chicken Curry'
        ];
        $this->createRecipes($recipeNames);
        
        $result = $this->recipeRepository->findByTitlePaginated();
        
        $this->assertCount(3, $result['recipes']);
        $this->assertEquals(3, $result['total']);
    }
    
    public function testFindByTitlePaginatedWithPagination(): void
    {
        $recipeNames = [
            'Pasta Carbonara',
            'Pasta Bolognese',
            'Pasta Alfredo',
            'Tomato Soup',
            'Chicken Curry'
        ];
        $this->createRecipes($recipeNames);
        
        $result = $this->recipeRepository->findByTitlePaginated('Pasta', 1, 2);
        
        $this->assertCount(2, $result['recipes']);
        $this->assertEquals(3, $result['total']);
    }
    
    public function testFindWithRelations(): void
    {
        $recipe = $this->createRecipeWithIngredients();
        
        $foundRecipe = $this->recipeRepository->findWithRelations($recipe->getId());

        $this->assertNotNull($foundRecipe);
        $this->assertEquals($recipe->getId(), $foundRecipe->getId());
        $this->assertCount(2, $foundRecipe->getIngredients());
    }

    private function createRecipes(array $titles): void
    {
        foreach ($titles as $title) {
            $recipe = new Recipe();
            $recipe->setExternalId('ext_' . md5($title));
            $recipe->setTitle($title);
            $recipe->setInstructions('Test instructions');
            $this->entityManager->persist($recipe);
        }
        
        $this->entityManager->flush();
    }

    private function createRecipeWithIngredients(): Recipe
    {
        $recipe = new Recipe();
        $recipe->setExternalId('ext_with_ingredients');
        $recipe->setTitle('Recipe With Ingredients');
        $recipe->setInstructions('Test instructions');
        
        $ingredient1 = new RecipeIngredient();
        $ingredient1->setName('Flour');
        $ingredient1->setMeasure('200g');
        $ingredient1->setRecipe($recipe);
        
        $ingredient2 = new RecipeIngredient();
        $ingredient2->setName('Sugar');
        $ingredient2->setMeasure('100g');
        $ingredient2->setRecipe($recipe);

        $recipe->addIngredient($ingredient1);
        $recipe->addIngredient($ingredient2);

        $this->entityManager->persist($recipe);
        $this->entityManager->persist($ingredient1);
        $this->entityManager->persist($ingredient2);
        $this->entityManager->flush();
        
        return $recipe;
    }
} 