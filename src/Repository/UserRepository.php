<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param User $user
     * @return void
     */
    public function remove(User $user): void
    {
        $this->_em->remove($user);
        $this->_em->flush();
    }

    /**
     * @param string $sort
     * @param mixed $order
     * @return QueryBuilder
     */
    public function findAllQueryBuilder(string $sort, mixed $order): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('u');

        if ($sort === 'roles') {
            $caseExpression = "CASE 
            WHEN u.roles LIKE '%ROLE_ADMIN%' THEN 1
            WHEN u.roles LIKE '%ROLE_SELLER%' THEN 2
            WHEN u.roles LIKE '%ROLE_CUSTOMER%' THEN 3
            ELSE 4
            END";

            $queryBuilder->orderBy($caseExpression, $order);
        } else {
            $queryBuilder->orderBy('u.' . $sort, $order);
        }

        return $queryBuilder;
    }

}