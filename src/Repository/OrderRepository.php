<?php

namespace App\Repository;


use App\Entity\Order;
use App\Entity\OrderItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @param Order $entity
     * @param bool $flush
     * @return void
     */
    public function save(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Order $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findCart(?int $id, ?UserInterface $user): ?Order
    {
        $builder = $this->createQueryBuilder('o');
        if ($id) {
            $builder->where('o.id = :id')
                ->setParameter('id', $id);

        }

        if ($user) {
            $builder->andWhere('o.customer = :customer')
                ->setParameter('customer', $user);
        };

        $builder->andWhere('o.status in (:status)')
            ->setParameter('status', [Order::STATUS_CART, Order::STATUS_PENDING]);
        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param \DateTime $limitDate
     * @param int $limit
     * @return array
     */
    public function findCartsNotModifiedSince(\DateTime $limitDate, int $limit = 10): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.status = :status')
            ->andWhere('o.updatedAt < :date')
            ->setParameter('status', Order::STATUS_CART)
            ->setParameter('date', $limitDate)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param UserInterface|null $user
     * @param mixed $sort
     * @param mixed $order
     * @param mixed $page
     * @param mixed $limit
     * @return QueryBuilder
     */
    public function findOrdersBySellerQuery(
        ?UserInterface $user,
        string         $sort = "createdAt",
        string         $order = "DESC",
        int            $page = null,
        int            $limit = null
    ): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->innerJoin('o.orderItemSellers', 'oi')
            ->innerJoin('oi.seller', 's')
            ->where('s.id = :seller')
            ->andWhere('o.status != :status')
            ->setParameter('status', Order::STATUS_CART);


        if ($sort) {
            $queryBuilder->orderBy('o.' . $sort, $order);
        }

        $queryBuilder->setParameter('seller', $user->getId());

        if ($limit) {
            $queryBuilder->setMaxResults($limit);
        }

        if ($page) {
            $queryBuilder->setFirstResult(($page - 1) * $limit);
        }

        return $queryBuilder;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOrderBySeller(?int $getId, ?int $getId1)
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->innerJoin('o.orderItemSellers', 'oi')
            ->innerJoin('oi.seller', 's')
            ->where('s.id = :seller')
            ->andWhere('o.id = :order')
            ->andWhere('o.status != :status')
            ->setParameter('status', Order::STATUS_CART)
            ->setParameter('seller', $getId1)
            ->setParameter('order', $getId);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param UserInterface|null $user
     * @return float|int|mixed|string
     */
    public function findOrdersBySeller(?UserInterface $user)
    {
        return $this->findOrdersBySellerQuery($user)->getQuery()->getResult();

    }

    /**
     * @param UserInterface|null $user
     * @param array $orderIds
     * @return float|int|mixed|string
     */
    public function findTotalIncomesPerMonth(?UserInterface $user, array $orderIds)
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder->select("DATE_FORMAT(o.createdAt, '%M') AS month", 'SUM(oi.quantity * p.price) AS totalIncome')
            ->leftJoin('o.orderItems', 'oi')
            ->leftJoin('oi.product', 'p')
            ->where('p.seller = :seller')
            ->andWhere($queryBuilder->expr()->in('o.id', $orderIds))
            ->setParameter('seller', $user)
            ->groupBy('month');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param UserInterface|null $user
     * @param array $orderIds
     * @param bool $isParent
     * @return float|int|mixed|string
     */
    public function findTotalProductSoldPerCategories(?UserInterface $user, array $orderIds, bool $isParent = false)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder->select('c.name AS categoryName', 'c.id AS categoryId', 'SUM(oi.quantity) AS totalQuantity')
            ->from(OrderItem::class, 'oi')
            ->leftJoin('oi.product', 'p')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('p.seller', 's')
            ->where('s = :seller');
        if ($isParent) {
            $queryBuilder->andWhere('c.parent IS NULL');
        }

        $queryBuilder->andWhere($queryBuilder->expr()->in('o.id', $orderIds))
            ->setParameter('seller', $user)
            ->groupBy('c.id');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param UserInterface|null $user
     * @param array $ordersIds
     * @return float|int|mixed|string
     */
    public function findTotalTopProductsSold(?UserInterface $user, array $ordersIds)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder->select('p.name AS productName', 'SUM(oi.quantity) AS totalQuantity')
            ->from(OrderItem::class, 'oi')
            ->leftJoin('oi.product', 'p')
            ->leftJoin('p.seller', 's')
            ->where('s = :seller')
            ->andWhere($queryBuilder->expr()->in('o.id', $ordersIds))
            ->setParameter('seller', $user)
            ->groupBy('p.id')
            ->orderBy('totalQuantity', 'DESC')
            ->setMaxResults(10);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param UserInterface $user
     * @param array $orderIds
     * @return float|int|mixed|string
     */
    public function findCartAveragePerMonth(UserInterface $user, array $orderIds)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder->select("DATE_FORMAT(o.createdAt, '%M') AS month", 'AVG(oi.quantity * p.price) AS averageCart')
            ->leftJoin('o.orderItems', 'oi')
            ->leftJoin('oi.product', 'p')
            ->where('p.seller = :seller')
            ->andWhere($queryBuilder->expr()->in('o.id', $orderIds))
            ->setParameter('seller', $user)
            ->groupBy('month');

        return $queryBuilder->getQuery()->getResult();
    }

}