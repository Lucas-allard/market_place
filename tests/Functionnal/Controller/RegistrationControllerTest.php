<?php

namespace App\Tests\Functionnal\Controller;

use App\Controller\RegistrationController;
use App\Entity\Customer;
use App\Entity\Seller;
use App\Security\EmailVerifier;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage
 */
class RegistrationControllerTest extends WebTestCase
{

    private RegistrationController $registrationController;
    private EmailVerifier $emailVerifier;

    private EntityManager $entityManager;

    private KernelBrowser $client;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->client = static::createClient([], ['environment' => 'test']);
        $this->client->disableReboot();
        $this->emailVerifier = $this->createMock(EmailVerifier::class);
        $this->registrationController = new RegistrationController($this->emailVerifier);
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $this->entityManager->getConnection()->beginTransaction();
    }

    /**
     * @group registration
     * @group registration-controller
     * @group registration-controller-can-get-register-page
     */
    public function testCanGetRegisterPage(): void
    {

        $crawler = $this->client->request('GET', '/inscription');

        $this->assertResponseIsSuccessful();
    }

    /**
     * @group registration
     * @group registration-controller
     * @group registration-controller-can-register-user
     * @throws NotSupported
     */
    public function testCanRegisterCustomer(): void
    {

        $crawler = $this->client->request('GET', '/inscription');

        $form = $crawler->selectButton('S\'inscrire')->form();

        $form['customer_registration[email]'] = 'register-customer@mail.com';
        $form['customer_registration[plainPassword]'] = 'password';
        $form['customer_registration[agreeTerms]'] = true;

        $this->client->submit($form);

        $this->assertResponseRedirects('/');

        $this->client->followRedirect();

        $customer = $this->entityManager->getRepository(Customer::class)->findOneBy(['email' => 'register-customer@mail.com']);

        $this->assertNotNull($customer);
    }

    /**
     * @group registration
     * @group registration-controller
     * @group registration-controller-can-register-seller
     *
     * @throws NotSupported
     */
    public function testCanRegisterSeller(): void
    {
        $crawler = $this->client->request('GET', '/inscription?type=seller');

        $form = $crawler->selectButton('S\'inscrire')->form();

        $form['seller_registration[email]'] = 'register-seller@mail.com';
        $form['seller_registration[plainPassword]'] = 'password';
        $form['seller_registration[agreeTerms]'] = true;

        $this->client->submit($form);

        $this->assertResponseRedirects('/');

        $this->client->followRedirect();

        $seller = $this->entityManager->getRepository(Seller::class)->findOneBy(['email' => 'register-seller@mail.com']);

        $this->assertNotNull($seller);
    }

    /**
     * @group registration
     * @group registration-controller
     * @group registration-controller-verify-email
     * @group registration-controller-verify-email-without-granting
     */
    public function testVerifyEmailWithoutGranting(): void
    {
        $this->client->request('GET', '/verification/email');

        $this->assertResponseRedirects('/');
    }

//    /**
//     * @group registration
//     * @group registration-controller
//     * @group registration-controller-verify-email
//     * @group registration-controller-verify-email-with-granting
//     * @throws NotSupported
//     * @throws ORMException
//     * @throws TransportExceptionInterface
//     */
//    public function testVerifyUserEmail()
//    {
//        // Crée un nouvel utilisateur
//        $user = new Customer();
//        $user->setEmail('verify-email@test.com');
//        $user->setPassword('password');
//        $this->entityManager->persist($user);
//        $this->entityManager->flush();
//
//        // Connecte l'utilisateur
//        $this->client->loginUser($user);
//
//        // Envoie l'e-mail de confirmation
//        $email = (new TemplatedEmail())
//            ->to($user->getEmail())
//            ->subject('Please Confirm your Email')
//            ->htmlTemplate('registration/confirmation_email.html.twig')
//            ->context([
//                'user' => $user,
//                'signedUrl' => 'http://localhost/verification/email?expires=1620000000&signature=1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef'
//            ]);
//
//        // send this email
//        $this->emailVerifier->sendEmailConfirmation('registration_verify_email', $user, $email);
//    }


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
            $this->entityManager->getConnection()->commit();
        }
        $this->entityManager->close();
    }
}