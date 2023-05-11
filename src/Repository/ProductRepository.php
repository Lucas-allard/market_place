<?php

namespace App\Repository;

use App\Entity\Product;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $product, bool $flush = false): void
    {
        $this->getEntityManager()->persist($product);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $product, bool $flush = false): void
    {
        $this->getEntityManager()->remove($product);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findTopProductsWithCategories(bool $isParentCategory = true, int $maxResults = 9)
    {

        $qb = $this->createQueryBuilder('p');

        $qb->select('c2.name as parent_category,c2.slug as parent_slug, c.name as children_category, c.slug as children_slug, p.name, p.slug, p.description, p.price')
            ->innerJoin('p.categories', 'c')
            ->innerJoin('c.parent', 'c2')
            ->innerJoin('p.orderItems', 'oi')
            ->innerJoin('oi.order', 'o')
            ->where('o.createdAt >= :oneMonthAgo')
            ->groupBy('p.id')
            ->orderBy('MAX(oi.quantity)', 'DESC')
            ->setMaxResults($maxResults);

        $qb->setParameter('oneMonthAgo', new DateTime('-1 MONTH'));

        return $qb->getQuery()->getResult();
    }

}