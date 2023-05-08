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
        $qb->select('parent.name AS parent_category, child.name AS child_category')
            ->leftJoin('parent.children', 'child')
            ->where('parent.parent IS NULL')
            ->orderBy('parent.id', 'ASC');

        $result = $qb->getQuery()->getResult();

        $categories = [];
        foreach ($result as $row) {
            $categories[$row['parent_category']][] = $row['child_category'];
        }
        return $categories;

    }
}