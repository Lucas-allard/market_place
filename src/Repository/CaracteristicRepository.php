<?php

namespace App\Repository;

use App\Entity\Caracteristic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Caracteristic>
 *
 * @method Caracteristic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Caracteristic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Caracteristic[]    findAll()
 * @method Caracteristic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CaracteristicRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Caracteristic::class);
    }

    /**
     * @param Caracteristic $entity
     * @param bool $flush
     * @return void
     */
    public function save(Caracteristic $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Caracteristic $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Caracteristic $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param string $sort
     * @param string $order
     * @return QueryBuilder
     */
    public function findAllQueryBuilder(string $sort, string $order): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('c');

        if ($sort && $order) {
            $queryBuilder->orderBy('c.' . $sort, $order);
        }

        return $queryBuilder;
    }
}
