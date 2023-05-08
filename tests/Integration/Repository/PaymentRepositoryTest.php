<?php

namespace App\Tests\Functional\Repository;

use App\Entity\Payment;
use App\Repository\PaymentRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PaymentRepositoryTest extends KernelTestCase
{

    private EntityManager $entityManager;

    private PaymentRepository $paymentRepository;

    /**
     * @throws NotSupported
     * @throws Exception
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $this->entityManager->getConnection()->beginTransaction();

        $this->paymentRepository = $this->entityManager->getRepository(Payment::class);
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(PaymentRepository::class, $this->paymentRepository);
    }

    /**
     * @group repository
     * @group payment-repository
     * @group payment-repository-save
     */
    public function testSave(): void
    {
        $payment = new Payment();
        $this->paymentRepository->save($payment, true);
        $this->assertNotNull($payment->getId());
    }

    /**
     * @group repository
     * @group payment-repository
     * @group payment-repository-remove
     */
    public function testRemove(): void
    {
        $payment = new Payment();
        $this->paymentRepository->save($payment, true);
        $this->paymentRepository->remove($payment, true);
        $this->assertNull($this->paymentRepository->findOneBy(['id' => $payment->getId()]));
    }


    /**
     * @throws Exception
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $connection = $this->entityManager->getConnection();

        if ($connection->isTransactionActive()) {
            $connection->rollBack();
        }

        $this->entityManager->close();
    }
}