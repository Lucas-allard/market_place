<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Product;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

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

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @param Product $product
     * @param bool $flush
     * @return void
     */
    public function save(Product $product, bool $flush = false): void
    {
        $this->getEntityManager()->persist($product);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Product $product
     * @param bool $flush
     * @return void
     */
    public function remove(Product $product, bool $flush = false): void
    {
        $this->getEntityManager()->remove($product);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param int $maxResults
     * @return Product[]
     */
    public function findTopProductsOrdered(int $maxResults = 9): array
    {

        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('p')
            ->from(Product::class, 'p')
            ->innerJoin('p.orderItems', 'oi')
            ->innerJoin('oi.order', 'o')
            ->innerJoin('p.categories', 'c')
            ->innerJoin('p.brand', 'b')
            ->where('o.createdAt <= :oneMonthAgo')
            ->andWhere('p.quantity > 0')
            ->groupBy('p.id')
            ->orderBy('MAX(oi.quantity)', 'DESC')
            ->setMaxResults($maxResults);

        $queryBuilder->setParameter('oneMonthAgo', new DateTime('-1 MONTH'));

        return $queryBuilder->getQuery()->getResult();
    }


    /**
     * @param int $maxResults
     * @param int|null $offset
     * @return Product[]
     */
    public function findNewsArrivalsProducts(int $maxResults = 9, ?int $offset = 0): array
    {

        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('p')
            ->from(Product::class, 'p')
            ->innerJoin('p.categories', 'c')
            ->innerJoin('p.orderItems', 'oi')
            ->where('p.createdAt >= :createdAt')
            ->andWhere('p.quantity > 0')
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

    /**
     * @param int $maxResult
     * @return Product[]
     */
    public function findSellsProductsHasDiscount(int $maxResult): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('p')
            ->from(Product::class, 'p')
            ->innerJoin('p.categories', 'c')
            ->innerJoin('p.orderItems', 'oi')
            ->andWhere('p.quantity > 0')
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

    /**
     * @param string $categoryId
     * @param string|null $parentCategoryId
     * @param string $order
     * @return QueryBuilder
     */
    public function getProductsByCategoryIdQuery(string $categoryId, ?string $parentCategoryId = null, string $order = "DESC"): QueryBuilder
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('p')
            ->from(Product::class, 'p')
            ->innerJoin('p.categories', 'c')
            ->where('c.id = :categoryId')
            ->andWhere('p.quantity > 0')
            ->setParameter('categoryId', $categoryId)
            ->orderBy('p.createdAt', $order);

        if ($parentCategoryId) {
            $queryBuilder
                ->innerJoin('c.parent', 'cp')
                ->andWhere('cp.id = :parentCategoryId')
                ->setParameter('parentCategoryId', $parentCategoryId);
        }

        return $queryBuilder;
    }


    /**
     * @param string $categoryId
     * @param string|null $parentCategoryId
     * @param int|null $minPrice
     * @param int|null $maxPrice
     * @param array|null $brandsId
     * @param array|null $caracteristicsId
     * @param string $order
     * @return QueryBuilder
     */
    public function getProductsByFilterQuery(
        string  $categoryId,
        ?string $parentCategoryId = null,
        ?int    $minPrice = null,
        ?int    $maxPrice = null,
        ?array  $brandsId = null,
        ?array  $caracteristicsId = null,
        string  $order = 'DESC'): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->innerJoin('p.categories', 'c')
            ->innerJoin('p.brand', 'b')
            ->innerJoin('p.caracteristics', 'car');

        if ($parentCategoryId) {
            $queryBuilder
                ->innerJoin('c.parent', 'cp')
                ->where('cp.id = :parentCategoryId')
                ->setParameter('parentCategoryId', $parentCategoryId);
        }

        $queryBuilder->andWhere('c.id = :categoryId')
            ->setParameter('categoryId', $categoryId);

        if ($minPrice) {
            $queryBuilder
                ->andWhere('p.price >= :minPrice')
                ->setParameter('minPrice', $minPrice);
        }

        if ($maxPrice) {
            $queryBuilder
                ->andWhere('p.price <= :maxPrice')
                ->setParameter('maxPrice', $maxPrice);
        }

        if ($brandsId) {
            $queryBuilder
                ->andWhere('b.id IN (:brandsId)')
                ->setParameter('brandsId', $brandsId);
        }

        if ($caracteristicsId) {
            $queryBuilder
                ->andWhere('car.id IN (:caracteristicsId)')
                ->setParameter('caracteristicsId', $caracteristicsId);
        }

        $queryBuilder
            ->andWhere('p.quantity > 0')
            ->orderBy('p.price', $order);

        return $queryBuilder;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findMinPrice()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('MIN(p.price)')
            ->from(Product::class, 'p');

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findMaxPrice()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->select('MAX(p.price)')
            ->from(Product::class, 'p');

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * @param array $categoryIds
     * @return array
     */
    public function findBestProductsByCategoryIds(array $categoryIds): array
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->select('p')
            ->addSelect('c.id AS categoryId')
            ->innerJoin('p.categories', 'c')
            ->innerJoin('p.orderItems', 'oi')
            ->where($queryBuilder->expr()->in('c.id', $categoryIds))
            ->andWhere('p.quantity > 0')
            ->andWhere('c.parent IS NOT NULL')
            ->groupBy('c.id')
            ->orderBy('SUM(oi.quantity)', 'DESC');

        $query = $queryBuilder->getQuery();
        $results = $query->getResult();

        $productsByCategory = [];
        foreach ($results as $result) {
            $categoryId = $result['categoryId'];
            $product = $result[0];
            $productsByCategory[$categoryId] = $product;
        }

        return $productsByCategory;
    }


    /**
     * @param UserInterface|null $user
     * @param string|null $sort
     * @param string|null $order
     * @return QueryBuilder
     */
    public function getProductsBySellerQuery(?UserInterface $user, ?string $sort, ?string $order): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->innerJoin('p.seller', 's')
            ->where('s.id = :seller');

        if ($sort) {
            $queryBuilder->orderBy('p.' . $sort, $order);
        }

        $queryBuilder
            ->setParameter('seller', $user->getId());
        return $queryBuilder;
    }

}