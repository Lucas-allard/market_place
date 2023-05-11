<?php

namespace App\Tests\Units\Controller;

use App\Controller\RegistrationController;
use App\DataFixtures\Security\EmailVerifier;
use App\Entity\Customer;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Mime\Address;

class RegistrationControllerTest extends TestCase
{

    private EmailVerifier $emailVerifier;
    private RegistrationController $registrationController;

    protected function setUp(): void
    {
        $this->emailVerifier = $this->getMockBuilder(EmailVerifier::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->registrationController = new RegistrationController($this->emailVerifier);
    }

    /**
     * @group registration
     * @group registration-controller-register
     * @group registration-controller-send-email-confirmation
     * @throws Exception
     */
    public function testSendEmailConfirmation(): void
    {
        // Crée un mock pour l'objet EmailVerifier
        // Configure le mock pour s'attendre à un appel de méthode avec les bons paramètres
        $user = new Customer();
        $user->setEmail('test-send-email@mail.com');
        $this->emailVerifier->expects($this->once())
            ->method('sendEmailConfirmation')
            ->with(
                'app_verify_email', // le nom de la route Symfony pour la confirmation de l'email
                $user, // l'utilisateur à qui envoyer l'email
                $this->callback(function (TemplatedEmail $email) {
                    return
                        $email->getFrom() == [new Address('noreply@marketplace.fr', 'Market Place')] &&
                        $email->getTo() == [new Address('test-send-email@mail.com')] &&
                        $email->getSubject() == 'Please Confirm your Email' &&
                        $email->getHtmlTemplate() == 'registration/confirmation_email.html.twig';
                })
            );

        // Appelle la méthode à tester en utilisant le mock pour l'objet EmailVerifier
        $registrationController = new RegistrationController($this->emailVerifier);
        $registrationController->sendEmailConfirmation($user);
    }

    /**
     * @group registration
     * @group registration-controller
     * @group registration-controller-form-is-valid
     *
     */
    public function testRegistrationFormIsValid()
    {
        // test this method : public function checkFormValid(FormInterface $form): bool
        //    {
        //        return $form->isSubmitted() && $form->isValid();
        //    }

        $form = $this->createMock(FormInterface::class);
        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(true);

        $this->assertTrue($this->registrationController->checkFormValid($form));
    }

    /**
     * @group registration
     * @group registration-controller
     * @group registration-controller-form-is-not-valid
     *
     */
    public function testRegistrationFormIsNotValid()
    {
        $form = $this->createMock(FormInterface::class);
        $form->method('isSubmitted')->willReturn(true);
        $form->method('isValid')->willReturn(false);

        $this->assertFalse($this->registrationController->checkFormValid($form));
    }

}