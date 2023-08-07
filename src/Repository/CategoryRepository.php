<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @param Category $entity
     * @param bool $flush
     * @return void
     */
    public function save(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Category $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param int $maxResults
     * @return array
     */
    public function findCategoriesHasOrder(int $maxResults = 24): array
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('c')
            ->leftJoin('c.products', 'p')
            ->leftJoin('p.orderItems', 'oi')
            ->where('c.parent IS NOT NULL')
            ->andWhere('oi.quantity > 0')
            ->groupBy('c.id')
            ->setMaxResults($maxResults);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function findChildrenCategories(): array
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.parent IS NOT NULL')
            ->orderBy('c.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string $sort
     * @param mixed $order
     * @return QueryBuilder
     */
    public function findAllQueryBuilder(string $sort, mixed $order): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('c');

        if ($sort === 'name') {
            $queryBuilder->leftJoin('c.parent', 'p')
                ->addOrderBy('p.name', $order)
                ->addOrderBy('c.name', $order);
        } else {
            $queryBuilder->orderBy('c.' . $sort, $order);
        }

        return $queryBuilder;
    }

    /**
     * @param array $productIds
     * @return array
     */
    public function findTotalProductPerCategories(array $productIds): array
    {
        return  $this->createQueryBuilder('c')
            ->select('c.name AS category', 'COUNT(p.id) AS total')
            ->leftJoin('c.products', 'p')
            ->where('p.id IN (:productIds)')
            ->groupBy('c.id')
            ->setParameter('productIds', $productIds)
            ->getQuery()
            ->getResult();
    }
}