<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function getParentCategories(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.parent IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function getChildrenCategories(Category $category): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.parent = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getResult();
    }

    public function getParentsAndChildrenCategoriesInSeparatedArrays(): array
    {
//        SELECT parent.name AS parent_category, child.name AS child_category
//FROM category AS parent
//LEFT JOIN category AS child ON child.parent_id = parent.id
//WHERE parent.parent_id IS NULL
//ORDER BY parent_category

        $qb = $this->createQueryBuilder('parent');
        $qb->select('parent.name AS parent_category, child.name AS child_category, parent.slug AS parent_slug, child.slug AS child_slug')
            ->leftJoin('parent.children', 'child')
            ->where('parent.parent IS NULL')
            ->orderBy('parent.id', 'ASC');

        $result = $qb->getQuery()->getResult();
        $categories = [];
//        foreach ($result as $row) {
////            $categories[$row['parent_category']][] = $row['child_category'];
//            // add the parent cat & parent slug to array and same for child
//
//        }
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
}