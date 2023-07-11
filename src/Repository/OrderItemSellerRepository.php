<?php

namespace App\Repository;

use App\Entity\OrderItemSeller;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderItemSeller>
 *
 * @method OrderItemSeller|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderItemSeller|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderItemSeller[]    findAll()
 * @method OrderItemSeller[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderItemSellerRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderItemSeller::class);
    }

    /**
     * @param OrderItemSeller $entity
     * @param bool $flush
     * @return void
     */
    public function save(OrderItemSeller $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param OrderItemSeller $entity
     * @param bool $flush
     * @return void
     */
    public function remove(OrderItemSeller $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return OrderItemSeller[] Returns an array of OrderItemSeller objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?OrderItemSeller
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
