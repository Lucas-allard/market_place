<?php

namespace App\Tests\Functional\Repository;

use App\DataFixtures\OrderFixture;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrderRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private OrderRepository $orderRepository;

    /**
     * @throws NotSupported
     * @throws Exception
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $this->entityManager->getConnection()->beginTransaction();

        $this->orderRepository = $this->entityManager->getRepository(Order::class);
    }

    /**
     * @group repository
     * @group order-repository
     * @group order-repository-constructor
     */
    public function testConstructor(): void
    {
        $this->assertInstanceOf(OrderRepository::class, $this->orderRepository);
    }

    /**
     * @group repository
     * @group order-repository
     * @group order-repository-find-all
     */
    public function testFindAll(): void
    {

        $orders = $this->orderRepository->findAll();
        $this->assertIsArray($orders);
        $this->assertNotEmpty($orders);
        $this->assertInstanceOf(Order::class, $orders[0]);
        $this->assertCount(OrderFixture::ORDER_COUNT, $orders);
    }

    /**
     * @group repository
     * @group order-repository
     * @group order-repository-find-one-by
     */
    public function testFindOneBy(): void
    {
        $order = $this->orderRepository->findOneBy(['id' => 1]);
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals(1, $order->getId());
    }

    /**
     * @group repository
     * @group order-repository
     * @group order-repository-find
     */
    public function testFind(): void
    {
        $order = $this->orderRepository->find(1);
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals(1, $order->getId());
    }

    /**
     * @group repository
     * @group order-repository
     * @group order-repository-find-by
     */
    public function testFindBy(): void
    {
        $orders = $this->orderRepository->findBy(['orderStatus' => Order::STATUS_PENDING]);
        $this->assertIsArray($orders);
        $this->assertNotEmpty($orders);
        $this->assertInstanceOf(Order::class, $orders[0]);
        $this->assertCount(OrderFixture::ORDER_COUNT, $orders);
        $this->assertEquals(1, $orders[0]->getId());
    }

    /**
     * @group repository
     * @group order-repository
     * @group order-repository-save
     */
    public function testSave(): void
    {
        $order = $this->orderRepository->find(1);
        $order->setOrderStatus(Order::STATUS_COMPLETED);

        $this->orderRepository->save($order, true);

        $order = $this->orderRepository->find(1);

        $this->assertEquals(Order::STATUS_COMPLETED, $order->getOrderStatus());
    }

    /**
     * @group repository
     * @group order-repository
     * @group order-repository-remove
     */
    public function testRemove(): void
    {
        $order = $this->orderRepository->find(1);

        $this->orderRepository->remove($order, true);

        $order = $this->orderRepository->find(1);

        $this->assertNull($order);
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