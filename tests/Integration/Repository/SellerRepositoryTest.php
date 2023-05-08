<?php

namespace App\Tests\Functional\Repository;

use App\Entity\Seller;
use App\Repository\SellerRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SellerRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private SellerRepository $sellerRepository;

    /**
     * @throws Exception
     * @throws NotSupported
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $this->entityManager->getConnection()->beginTransaction();

        $this->sellerRepository = $this->entityManager->getRepository(Seller::class);
    }

    /**
     * @group repository
     * @group seller-repository
     * @group seller-repository-constructor
     */
    public function testConstructor(): void
    {
        $this->assertInstanceOf(SellerRepository::class, $this->sellerRepository);
    }

    /**
     * @group repository
     * @group seller-repository
     * @group seller-repository-save
     */
    public function testSave(): void
    {
        $seller = new Seller();
        $seller->setCompany('Company 1');
        $this->sellerRepository->save($seller, true);
        $this->assertNotNull($seller->getId());
    }


    /**
     * @group repository
     * @group seller-repository
     * @group seller-repository-remove
     */
    public function testRemove(): void
    {
        $seller = new Seller();
        $seller->setCompany('Company 1');
        $this->sellerRepository->save($seller, true);
        $this->assertNotNull($seller->getId());
        $this->sellerRepository->remove($seller, true);
        $seller = $this->sellerRepository->findOneBy(['id' => $seller->getId()]);
        $this->assertNull($seller);
    }


    /**
     * @throws Exception
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