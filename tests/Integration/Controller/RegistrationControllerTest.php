<?php

namespace App\Tests\Integration\Controller;

use App\Controller\RegistrationController;
use App\Entity\Customer;
use App\Entity\Seller;
use App\Form\RegistrationForm\CustomerRegistrationType;
use App\Form\RegistrationForm\SellerRegistrationType;
use Doctrine\ORM\EntityManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationControllerTest extends KernelTestCase
{
    private RegistrationController $registrationController;
    private EntityManager $entityManager;
    private UserPasswordHasherInterface $userPasswordHasher;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->registrationController = $kernel->getContainer()->get(RegistrationController::class);
        $this->userPasswordHasher = static::getContainer()->get('security.user_password_hasher');
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->entityManager->getConnection()->beginTransaction();
    }

    /**
     * @group registration
     * @group registration-controller-create-form-type
     * @group registration-controller-create-form-type-customer
     */
    public function testCreateFormTypeReturnsCustomerRegistrationForm(): void
    {
        $formType = $this->registrationController->createFormType('customer');
        $this->assertInstanceOf(FormInterface::class, $formType);
        $this->assertInstanceOf(CustomerRegistrationType::class, $formType->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(Customer::class, $formType->getData());
    }

    /**
     * @group registration
     * @group registration-controller-create-form-type
     * @group registration-controller-create-form-type-seller
     */
    public function testCreateFormTypeReturnsSellerRegistrationForm(): void
    {
        $formType = $this->registrationController->createFormType('seller');
        $this->assertInstanceOf(FormInterface::class, $formType);
        $this->assertInstanceOf(SellerRegistrationType::class, $formType->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(Seller::class, $formType->getData());
    }

    /**
     * @group registration
     * @group registration-controller-create-form-type
     * @group registration-controller-create-form-type-exception
     */
    public function testCreateFormTypeThrowsException(): void
    {
        $this->expectException(NotFoundHttpException::class);
        $this->registrationController->createFormType('test');
    }

    /**
     * @group registration
     * @group registration-controller-register
     * @group registration-controller-encode-password
     * @throws Exception
     */
    public function testRegisterEncodePassword(): void
    {
        $user = new Customer();
        $password = 'password';

        $this->registrationController->encodePassword($user, $password, $this->userPasswordHasher);

        $this->assertNotEquals($password, $user->getPassword());
    }

    /**
     * @group registration
     * @group registration-controller-register
     * @group registration-controller-persist
     * @throws Exception
     */
    public function testRegisterPersistUser(): void
    {
        $user = new Customer();

        $this->registrationController->persistUser($user, $this->entityManager);

        $this->assertNotNull($user->getId());
    }

    /**
     * @throws \Doctrine\DBAL\Exception
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
