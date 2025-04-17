<?php

namespace App\Tests\Service\Recipe;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use App\Service\Recipe\RecipeListingService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RecipeListingServiceTest extends TestCase
{
    private RecipeListingService $recipeListingService;
    private MockObject|RecipeRepository $recipeRepository;

    protected function setUp(): void
    {
        $this->recipeRepository = $this->createMock(RecipeRepository::class);
        $this->recipeListingService = new RecipeListingService($this->recipeRepository);
    }

    public function testGetRecipesWithoutSearchTerm(): void
    {
        $page = 1;
        $limit = 10;
        $mockRecipes = $this->createMockRecipes(3);
        $total = count($mockRecipes);
        
        $this->recipeRepository
            ->expects($this->once())
            ->method('findByTitlePaginated')
            ->with(null, $page, $limit)
            ->willReturn([
                'recipes' => $mockRecipes,
                'total' => $total,
            ]);

        $result = $this->recipeListingService->getRecipes(null, $page, $limit);

        $this->assertCount(3, $result['recipes']);
        $this->assertEquals($total, $result['total']);
        $this->assertEquals(1, $result['lastPage']);
    }

    public function testGetRecipesWithSearchTerm(): void
    {
        $searchTerm = 'pasta';
        $page = 1;
        $limit = 10;
        $mockRecipes = $this->createMockRecipes(2);
        $total = count($mockRecipes);
        
        $this->recipeRepository
            ->expects($this->once())
            ->method('findByTitlePaginated')
            ->with($searchTerm, $page, $limit)
            ->willReturn([
                'recipes' => $mockRecipes,
                'total' => $total,
            ]);

        $result = $this->recipeListingService->getRecipes($searchTerm, $page, $limit);

        $this->assertCount(2, $result['recipes']);
        $this->assertEquals($total, $result['total']);
        $this->assertEquals(1, $result['lastPage']);
    }

    public function testGetRecipesWithPagination(): void
    {
        $page = 2;
        $limit = 5;
        $mockRecipes = $this->createMockRecipes(5);
        $total = 12;
        
        $this->recipeRepository
            ->expects($this->once())
            ->method('findByTitlePaginated')
            ->with(null, $page, $limit)
            ->willReturn([
                'recipes' => $mockRecipes,
                'total' => $total,
            ]);

        $result = $this->recipeListingService->getRecipes(null, $page, $limit);

        $this->assertCount(5, $result['recipes']);
        $this->assertEquals($total, $result['total']);
        $this->assertEquals(3, $result['lastPage']);
    }

    public function testGetRecipesWithEmptyResults(): void
    {
        $searchTerm = 'nonexistentrecipe';
        $page = 1;
        $limit = 10;
        
        $this->recipeRepository
            ->expects($this->once())
            ->method('findByTitlePaginated')
            ->with($searchTerm, $page, $limit)
            ->willReturn([
                'recipes' => [],
                'total' => 0,
            ]);

        $result = $this->recipeListingService->getRecipes($searchTerm, $page, $limit);

        $this->assertEmpty($result['recipes']);
        $this->assertEquals(0, $result['total']);
        $this->assertEquals(1, $result['lastPage']);
    }

    public function testSearchTermNormalization(): void
    {
        $searchTerm = '  pasta  ';
        $normalizedSearchTerm = 'pasta';
        $page = 1;
        $limit = 10;
        $mockRecipes = $this->createMockRecipes(2);
        
        $this->recipeRepository
            ->expects($this->once())
            ->method('findByTitlePaginated')
            ->with($normalizedSearchTerm, $page, $limit)
            ->willReturn([
                'recipes' => $mockRecipes,
                'total' => count($mockRecipes),
            ]);

        $result = $this->recipeListingService->getRecipes($searchTerm, $page, $limit);

        $this->assertCount(2, $result['recipes']);
    }

    public function testEmptySearchTermIsConvertedToNull(): void
    {
        $searchTerm = '';
        $page = 1;
        $limit = 10;
        $mockRecipes = $this->createMockRecipes(5);
        
        $this->recipeRepository
            ->expects($this->once())
            ->method('findByTitlePaginated')
            ->with(null, $page, $limit)
            ->willReturn([
                'recipes' => $mockRecipes,
                'total' => count($mockRecipes),
            ]);

        $result = $this->recipeListingService->getRecipes($searchTerm, $page, $limit);

        $this->assertCount(5, $result['recipes']);
    }

    public function testPageAndLimitDefaults(): void
    {
        $expectedPage = 1;
        $expectedLimit = 10;
        $mockRecipes = $this->createMockRecipes(10);
        
        $this->recipeRepository
            ->expects($this->once())
            ->method('findByTitlePaginated')
            ->with(null, $expectedPage, $expectedLimit)
            ->willReturn([
                'recipes' => $mockRecipes,
                'total' => count($mockRecipes),
            ]);

        $result = $this->recipeListingService->getRecipes();

        $this->assertCount(10, $result['recipes']);
    }

    private function createMockRecipes(int $count): array
    {
        $recipes = [];
        
        for ($i = 0; $i < $count; $i++) {
            $recipe = $this->createMock(Recipe::class);
            $recipe->method('getId')->willReturn($i + 1);
            $recipe->method('getTitle')->willReturn('Recipe ' . ($i + 1));
            
            $recipes[] = $recipe;
        }
        
        return $recipes;
    }
} 