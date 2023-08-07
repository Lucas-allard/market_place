<?php

namespace App\Controller;

use App\Security\EmailVerifier;
use App\Entity\Customer;
use App\Entity\Interface\UserInterface;
use App\Entity\Seller;
use App\Form\RegistrationForm\CustomerRegistrationType;
use App\Form\RegistrationForm\SellerRegistrationType;
use App\Security\UserAuthenticator;
use App\Service\Form\FormProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    /**
     * @var EmailVerifier
     */
    private EmailVerifier $emailVerifier;

    /**
     * @var FormProcessor
     */
    private FormProcessor $formProcessor;

    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * @param EmailVerifier $emailVerifier
     * @param FormProcessor $formProcessor
     * @param ValidatorInterface $validator
     */
    public function __construct(EmailVerifier $emailVerifier, FormProcessor $formProcessor, ValidatorInterface $validator)
    {
        $this->emailVerifier = $emailVerifier;
        $this->formProcessor = $formProcessor;
        $this->validator = $validator;
    }

    /**
     */
    #[Route('/inscription', name: 'app_register', requirements: ['type' => 'customer|seller'])]
    public function register(
        Request                     $request,
        EntityManagerInterface      $entityManager,
        UserAuthenticatorInterface  $userAuthenticator,
        UserAuthenticator           $authenticator,
    ): Response
    {
        $userType = $request->get('type') ?? 'customer';
        $form = $this->createFormType($userType);

        $this->formProcessor->handleRequest($request, $form);

        if ($this->checkFormValid($form)) {
            $user = $form->getData();
            $password = $form->get('password')->getData();
            $user->setPassword($password);
            $this->persistUser($user, $entityManager);
            $this->sendEmailConfirmation($user);

            $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @param string $type
     * @return FormInterface
     */
    public function createFormType(string $type): FormInterface
    {
        if ($type === 'customer') {
            return $this->formProcessor->create(CustomerRegistrationType::class, new Customer());
        } elseif ($type === 'seller') {
            return $this->formProcessor->create(SellerRegistrationType::class, new Seller());
        } else {
            throw $this->createNotFoundException('Registration type not found');
        }
    }

    /**
     * @param FormInterface $form
     * @return bool
     */
    public function checkFormValid(FormInterface $form): bool
    {
        if (!$form->isSubmitted() ) {
            return false;
        }
        $errors = $this->validator->validate($form, null, ['registration']);

        if (count($errors) > 0) {
            return false;
        }

        return true;
    }


    /**
     * @param UserInterface $user
     * @param EntityManagerInterface $entityManager
     * @return void
     */
    public function persistUser(UserInterface $user, EntityManagerInterface $entityManager): void
    {
        $entityManager->persist($user);
        $entityManager->flush();
    }

    /**
     */
    public function sendEmailConfirmation(UserInterface $user): void
    {
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('noreply@marketplace.fr', 'Market Place'))
                ->to($user->getEmail())
                ->subject('Merci de confirmer votre email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/verification/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {

            return $this->redirectToRoute('app_home');
        }

        /** @var UserInterface $user */
        $user = $this->getUser();
        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('app_home');
        }

        $this->addFlash('success', 'Votre email a bien été vérifié.');

        return $this->redirectToRoute('app_register');
    }
}
