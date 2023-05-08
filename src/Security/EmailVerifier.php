<?php

namespace App\Security;

use App\Entity\Interface\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerifier
{
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmailConfirmation(string $verifyEmailRouteName, UserInterface $user, TemplatedEmail $email): void
    {
        $signatureComponents = $this->generateSignature($verifyEmailRouteName, $user->getId(), $user->getEmail());

        $context = $email->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        $email->context($context);

        $this->sendEmail($email);
    }

    /**
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmationRequest(Request $request, UserInterface $user): void
    {
        $this->validateEmailConfirmationRequest($request->getUri(), $user->getId(), $user->getEmail());
        $this->markUserAsVerified($user);
    }

    public function generateSignature(string $verifyEmailRouteName, int $userId, string $email): VerifyEmailSignatureComponents
    {
        return $this->verifyEmailHelper->generateSignature($verifyEmailRouteName, (string)$userId, $email);
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function sendEmail(TemplatedEmail $email): void
    {
        $this->mailer->send($email);
    }

    /**
     * @throws VerifyEmailExceptionInterface
     */
    private function validateEmailConfirmationRequest(string $uri, int $userId, string $email): void
    {
        $this->verifyEmailHelper->validateEmailConfirmation($uri, (string)$userId, $email);
    }

    private function markUserAsVerified(UserInterface $user): void
    {
        $user->setIsVerified(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
