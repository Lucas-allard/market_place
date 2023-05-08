<?php

namespace App\Tests\Functional\Repository;

use App\DataFixtures\OrderItemFixture;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Repository\OrderItemRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrderItemRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;
    private OrderItemRepository $orderItemRepository;


    /**
     * @throws NotSupported
     * @throws Exception
     */
    public function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $this->entityManager->getConnection()->beginTransaction();

        $this->orderItemRepository = $this->entityManager->getRepository(OrderItem::class);
    }

    /**
     * @group repository
     * @group order-item-repository
     * @group order-item-repository-constructor
     */
    public function testConstructor(): void
    {
    $this->assertInstanceOf(OrderItemRepository::class, $this->orderItemRepository);
    }

    /**
     * @group repository
     * @group order-item-repository
     * @group order-item-repository-find-all
     */
    public function testFindAll(): void
    {
        $orderItems = $this->orderItemRepository->findAll();
        $this->assertIsArray($orderItems);
        $this->assertNotEmpty($orderItems);
        $this->assertInstanceOf(OrderItem::class, $orderItems[0]);
        $this->assertCount(OrderItemFixture::ORDER_ITEM_COUNT, $orderItems);
    }

    /**
     * @group repository
     * @group order-item-repository
     * @group order-item-repository-find-one-by
     */

    public function testFindOneBy(): void
    {
        $orderItem = $this->orderItemRepository->findOneBy(['id' => 1]);
        $this->assertInstanceOf(OrderItem::class, $orderItem);
        $this->assertEquals(1, $orderItem->getId());
    }

    /**
     * @group repository
     * @group order-item-repository
     * @group order-item-repository-find
     */
    public function testFind(): void
    {
        $orderItem = $this->orderItemRepository->find(1);
        $this->assertInstanceOf(OrderItem::class, $orderItem);
        $this->assertEquals(1, $orderItem->getId());
    }

    /**
     * @group repository
     * @group order-item-repository
     * @group order-item-repository-find-by
     */
    public function testFindBy(): void
    {
        $orderItems = $this->orderItemRepository->findBy(['id' => 1]);
        $this->assertIsArray($orderItems);
        $this->assertNotEmpty($orderItems);
        $this->assertInstanceOf(OrderItem::class, $orderItems[0]);
        $this->assertEquals(1, $orderItems[0]->getId());
    }

    /**
     * @group repository
     * @group order-item-repository
     * @group order-item-repository-save
     * @throws NotSupported
     */
    public function testSave(): void
    {
        $orderItem = new OrderItem();
        $orderItem->setOrder($this->entityManager->getRepository(Order::class)->findOneBy(['id' => 1]));
        $orderItem->setProduct($this->entityManager->getRepository(Product::class)->findOneBy(['id' => 1]));
        $orderItem->setQuantity(1);
        $orderItem->setPrice(1);
        $this->orderItemRepository->save($orderItem, true);
        $this->assertNotNull($orderItem->getId());
    }

    /**
     * @group repository
     * @group order-item-repository
     * @group order-item-repository-remove
     */
    public function testRemove(): void
    {
        $orderItem = $this->orderItemRepository->findOneBy(['id' => 1]);
        $this->orderItemRepository->remove($orderItem, true);
        $orderItem = $this->orderItemRepository->findOneBy(['id' => 1]);
        $this->assertNull($orderItem);
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