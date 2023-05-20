<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Product;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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

    public function findTopProductsOrdered(int $maxResults = 9)
    {

        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('p, c, b')
            ->from(Product::class, 'p')
            ->innerJoin('p.orderItems', 'oi')
            ->innerJoin('oi.order', 'o')
            ->innerJoin('p.categories', 'c')
            ->innerJoin('p.brand', 'b')
            ->where('o.createdAt <= :oneMonthAgo')
            ->groupBy('p.id')
            ->orderBy('MAX(oi.quantity)', 'DESC')
            ->setMaxResults($maxResults);

        $queryBuilder->setParameter('oneMonthAgo', new DateTime('-1 MONTH'));

        return $queryBuilder->getQuery()->getResult();
    }


    public function findNewsArrivalsProducts(int $maxResults = 9, ?int $offset = 0)
    {

        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('p')
            ->from(Product::class, 'p')
            ->innerJoin('p.categories', 'c')
            ->innerJoin('p.orderItems', 'oi')
            ->where('p.createdAt >= :createdAt')
            ->andWhere($queryBuilder->expr()->in(
                'c.id',
                $this->getEntityManager()->createQueryBuilder()
                    ->select('c2.id')
                    ->from(Category::class, 'c2')
                    ->innerJoin('c2.products', 'p2')
                    ->innerJoin('p2.orderItems', 'oi2')
                    ->groupBy('c2.id')
                    ->orderBy('SUM(oi2.quantity)', 'DESC')
                    ->setMaxResults(3)
                    ->getDQL()
            ))
            ->setParameter('createdAt', new DateTime('-2 MONTH'))
            ->groupBy('p.id')
            ->setFirstResult($offset)
            ->setMaxResults($maxResults);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findSellsProductsHasDiscount(int $maxResult)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('p')
            ->from(Product::class, 'p')
            ->innerJoin('p.categories', 'c')
            ->innerJoin('p.orderItems', 'oi')
            ->where($queryBuilder->expr()->isNotNull('p.discount'))
            ->andWhere($queryBuilder->expr()->in(
                'c.id',
                $this->getEntityManager()->createQueryBuilder()
                    ->select('c2.id')
                    ->from(Category::class, 'c2')
                    ->innerJoin('c2.products', 'p2')
                    ->innerJoin('p2.orderItems', 'oi2')
                    ->groupBy('c2.id')
                    ->orderBy('SUM(oi2.quantity)', 'DESC')
                    ->getDQL()
            ))
            ->groupBy('p.id')
            ->orderBy('SUM(oi.quantity)', 'DESC')
            ->setMaxResults($maxResult);

        return $queryBuilder->getQuery()->getResult();
    }
}