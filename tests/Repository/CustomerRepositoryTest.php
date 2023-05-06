<?php

namespace App\Tests\Repository;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CustomerRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;
    private CustomerRepository $customerRepository;

    /**
     * @throws NotSupported
     * @throws Exception
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->entityManager->getConnection()->beginTransaction();
        $this->customerRepository = $this->entityManager->getRepository(Customer::class);
    }

    /**
     * @group repository
     * @group customer-repository
     * @group customer-repository-constructor
     */
    public function testConstructor(): void
    {
        $this->assertInstanceOf(CustomerRepository::class, $this->customerRepository);
    }

    /**
     * @group repository
     * @group customer-repository
     * @group customer-repository-save
     */
    public function testSave(): void
    {
        $customer = new Customer();
        $customer->setFirstName('John');
        $this->customerRepository->save($customer, true);
        $this->assertNotNull($customer->getId());
    }

    /**
     * @group repository
     * @group customer-repository
     * @group customer-repository-remove
     */
    public function testRemove(): void
    {
        $customer = new Customer();
        $customer->setFirstName('John');
        $this->customerRepository->save($customer, true);
        $this->assertNotNull($customer->getId());
        $this->customerRepository->remove($customer, true);
        $this->assertNull($this->customerRepository->findOneBy(['id' => $customer->getId()]));
    }

    /**
     * @group repository
     * @group customer-repository
     * @group customer-repository-find-all
     */
    public function testFindAll(): void
    {
        $customers = $this->customerRepository->findAll();
        $this->assertIsArray($customers);
        $this->assertNotEmpty($customers);
        $this->assertInstanceOf(Customer::class, $customers[0]);
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