<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 *
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    /**
     * Find recipes by title with pagination
     *
     * @param string|null $title Optional title filter
     * @param int $page Current page
     * @param int $limit Items per page
     * @return array{total: int, recipes: array}
     */
    public function findByTitlePaginated(?string $title = null, int $page = 1, int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('r')
            ->orderBy('r.title', 'ASC');

        if ($title) {
            $qb->andWhere('r.title LIKE :title')
                ->setParameter('title', '%' . $title . '%');
        }

        // Get total count for pagination
        $countQb = clone $qb;
        $total = $countQb->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult();

        // Add pagination
        $recipes = $qb->setMaxResults($limit)
            ->setFirstResult(($page - 1) * $limit)
            ->getQuery()
            ->getResult();

        return [
            'total' => $total,
            'recipes' => $recipes,
        ];
    }

    /**
     * Find recipe with all relations loaded
     *
     * @param int $id Recipe ID
     * @return Recipe|null
     */
    public function findWithRelations(int $id): ?Recipe
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.ingredients', 'i')
            ->addSelect('i')
            ->andWhere('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
