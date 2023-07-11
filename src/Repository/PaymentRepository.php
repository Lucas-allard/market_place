<?php

namespace App\Repository;

use App\Entity\Payment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payment>
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    /**
     * @param Payment $payment
     * @param bool $flush
     * @return void
     */
    public function save(Payment $payment, bool $flush = true): void
    {
        $this->getEntityManager()->persist($payment);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Payment $payment
     * @param bool $flush
     * @return void
     */
    public function remove(Payment $payment, bool $flush = true): void
    {
        $this->getEntityManager()->remove($payment);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}