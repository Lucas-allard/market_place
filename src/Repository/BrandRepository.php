<?php

namespace App\Repository;

use App\Entity\Brand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Brand>
 *
 * @method Brand|null find($id, $lockMode = null, $lockVersion = null)
 * @method Brand|null findOneBy(array $criteria, array $orderBy = null)
 * @method Brand[]    findAll()
 * @method Brand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BrandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Brand::class);
    }

    public function save(Brand $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Brand $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findBrandsWithPictures(): ?array
    {
        return $this->createQueryBuilder('b')
            ->where('b.picture IS NOT NULL')
            ->innerJoin('b.picture', 'p')
            ->addSelect('p')
            ->getQuery()
            ->getResult();
    }

    public function findTopBrands(int $limit = 16): ?array
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->select('b, p, pp')
            ->innerJoin('b.products', 'p')
            ->innerJoin('p.orderItems', 'oi')
            ->innerJoin('oi.order', 'o')
            ->innerJoin('p.pictures', 'pp')
            ->where('o.createdAt >= :createdAt')
            ->groupBy('b.id')
            ->orderBy('COUNT(oi.id)', 'DESC')
            ->addOrderBy('p.discount', 'DESC')
            ->setParameter('createdAt', new \DateTime('-2 months'))
            ->setMaxResults($limit);


        return $queryBuilder->getQuery()->getResult();
    }
}
