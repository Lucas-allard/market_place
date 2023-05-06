<?php

namespace App\Tests\Repository;

use _PHPStan_532094bc1\Nette\Neon\Entity;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private ProductRepository $productRepository;

    /**
     * @throws NotSupported
     * @throws Exception
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $this->entityManager->getConnection()->beginTransaction();

        $this->productRepository = $this->entityManager->getRepository(Product::class);
    }


    /**
     * @group repository
     * @group product-repository
     * @group product-repository-constructor
     */
    public function testConstructor(): void
    {
        $this->assertInstanceOf(ProductRepository::class, $this->productRepository);
    }

/**
     * @group repository
     * @group product-repository
     * @group product-repository-save
     */
    public function testSave(): void
    {
        $product = new Product();
        $this->productRepository->save($product, true);
        $this->assertNotNull($product->getId());
    }

    /**
     * @group repository
     * @group product-repository
     * @group product-repository-remove
     */
    public function testRemove(): void
    {
        $product = new Product();
        $this->productRepository->save($product, true);
        $this->productRepository->remove($product, true);
        $this->assertNull($this->productRepository->findOneBy(['id' => $product->getId()]));
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public
    function tearDown(): void
    {
        parent::tearDown();

        $connection = $this->entityManager->getConnection();

        if ($connection->isTransactionActive()) {
            $connection->rollBack();
        }

        $this->entityManager->close();
    }
}