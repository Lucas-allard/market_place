<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Seller;
use App\Form\Registration\CustomerRegistrationType;
use App\Form\Registration\SellerRegistrationType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/inscription', name: 'app_register', requirements: ['type' => 'customer|seller'], methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $userType = $request->get('type') ?? 'customer';
        $form = $this->createFormType($userType);
        $form->handleRequest($request);

        if ($this->checkFormValid($form)) {
            $user = $form->getData();
            $this->encodePassword($user, $form->get('plainPassword')->getData(), $userPasswordHasher);
            $this->persistUser($user, $entityManager);
            $this->sendEmailConfirmation($user);
            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    public function createFormType($type): FormInterface
    {
        if ($type === 'customer') {
            return $this->createForm(CustomerRegistrationType::class, new Customer());
        } elseif ($type === 'seller') {
            return $this->createForm(SellerRegistrationType::class, new Seller());
        } else {
            throw $this->createNotFoundException('Registration type not found');
        }
    }

    public function checkFormValid(FormInterface $form): bool
    {
        return $form->isSubmitted() && $form->isValid();
    }

    public function encodePassword($user, $plainPassword, $userPasswordHasher)
    {
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $plainPassword
            )
        );
    }

    public function persistUser($user, $entityManager)
    {
        $entityManager->persist($user);
        $entityManager->flush();
    }

    public function sendEmailConfirmation($user)
    {
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('noreply@marketplace.fr', 'Market Place'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }

    #[Route('/verification/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_home');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmationRequest($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
dd($exception->getReason());
            return $this->redirectToRoute('app_home');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
