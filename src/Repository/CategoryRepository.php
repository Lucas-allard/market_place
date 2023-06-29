<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\OrderItem;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Query\Expr\Join;
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

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function save(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function getChildrenCategories(Category $category): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.parent = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getResult();
    }

    public function findParentsAndChildrenCategoriesInSeparatedArrays(): array
    {
        $qb = $this->createQueryBuilder('parent');
        $qb->select('parent.name AS parent_category, child.name AS child_category, parent.slug AS parent_slug, child.slug AS child_slug')
            ->leftJoin('parent.children', 'child')
            ->where('parent.parent IS NULL')
            ->orderBy('parent.id', 'ASC');

        $result = $qb->getQuery()->getResult();
        $categories = [];

        foreach ($result as $row) {
            $parent_category = $row['parent_category'];
            $child_category = $row['child_category'];
            $parent_slug = $row['parent_slug'];
            $child_slug = $row['child_slug'];

            if (!isset($categories[$parent_category])) {
                $categories[$parent_category] = [
                    'name' => $parent_category,
                    'slug' => $parent_slug,
                    'children' => []
                ];
            }

            if ($child_category !== null) {
                $categories[$parent_category]['children'][] = [
                    'name' => $child_category,
                    'slug' => $child_slug
                ];
            }
        }

        return $categories;

    }


//    /**
//     * @throws Exception
//     */
//    public function findCategoriesHavingMostProductsAndBestProduct(int $maxResults = 28): array
//    {
//        $rawSql = <<<SQL
//SELECT c.*,
//       COUNT(p.id) AS total_products,
//       (SELECT p.name
//        FROM product p
//                 INNER JOIN category_product cp ON p.id = cp.product_id
//                 INNER JOIN order_item oi ON p.id = oi.product_id
//        WHERE cp.category_id = c.id
//          AND oi.quantity > 0
//        GROUP BY p.id
//        ORDER BY SUM(oi.quantity) DESC
//        LIMIT 1)       AS best_selling_product,
//       (SELECT pi.path
//        FROM product p
//                 INNER JOIN category_product cp ON p.id = cp.product_id
//                 INNER JOIN order_item oi ON p.id = oi.product_id
//                 INNER JOIN picture pi ON p.id = pi.product_id
//        WHERE cp.category_id = c.id
//          AND oi.quantity > 0
//        GROUP BY p.id
//        ORDER BY SUM(oi.quantity) DESC
//        LIMIT 1)       AS best_selling_product_picture
//FROM category c
//         INNER JOIN category_product cp ON c.id = cp.category_id
//         INNER JOIN product p ON cp.product_id = p.id
//WHERE c.parent_id IS NOT NULL
//GROUP BY c.id
//HAVING total_products > 0
//   AND best_selling_product IS NOT NULL
//ORDER BY total_products DESC
//LIMIT 24;
//SQL;
//
//        $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
//
//        return $stmt->executeQuery()->fetchAllAssociative();
//    }

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

    public function findChildrenCategories()
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.parent IS NOT NULL')
            ->orderBy('c.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
}