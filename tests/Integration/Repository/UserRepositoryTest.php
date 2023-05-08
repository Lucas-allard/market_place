<?php

namespace App\Tests\Functional\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private EntityManager $entityManager;

    private UserRepository $userRepository;

    /**
     * @throws NotSupported
     * @throws Exception
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $this->entityManager->getConnection()->beginTransaction();

        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    /**
     * @group repository
     * @group user-repository
     * @group user-repository-constructor
     */
    public function testConstructor(): void
    {
        $this->assertInstanceOf(UserRepository::class, $this->userRepository);
    }

    /**
     * @group repository
     * @group user-repository
     * @group user-repository-save
     */
    public function testFindAll(): void
    {
        $users = $this->userRepository->findAll();
        $this->assertIsArray($users);
        $this->assertNotEmpty($users);
        $this->assertInstanceOf(User::class, $users[0]);
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