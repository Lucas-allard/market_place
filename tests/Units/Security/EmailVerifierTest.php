<?php

namespace App\Tests\Units\Security;

use App\Entity\Interface\UserInterface;
use App\Security\EmailVerifier;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerifierTest extends TestCase
{

    private EmailVerifier $emailVerifier;
    private VerifyEmailHelperInterface $verifyEmailHelper;
    private MailerInterface $mailer;
    private EntityManagerInterface $entityManager;

    public function setUp(): void
    {
        $this->verifyEmailHelper = $this->createMock(VerifyEmailHelperInterface::class);
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->emailVerifier = new EmailVerifier(
            $this->verifyEmailHelper,
            $this->mailer,
            $this->entityManager
        );
    }

    /**
     * @group email
     * @group email-verifier
     * @group email-verifier-send-email-confirmation
     * @throws TransportExceptionInterface
     */
    public function testSendEmailConfirmation(): void
    {
        $user = $this->createMock(UserInterface::class);
        $user->method('getId')->willReturn(1);
        $user->method('getEmail')->willReturn('register@test.com');
        $email = $this->createMock(TemplatedEmail::class);
        $routeName = 'app_verify_email';

        $this->verifyEmailHelper->expects($this->once())
            ->method('generateSignature')
            ->with($routeName, $user->getId(), $user->getEmail())
            ->willReturn(new VerifyEmailSignatureComponents(
                new DateTime('+1 day'),
                'signedUrl',
                (new DateTime)->getTimestamp(),
            ));


        $this->mailer->expects($this->once())
            ->method('send')
            ->with($this->callback(function (TemplatedEmail $email) {
                $context = $email->getContext();
                $context['signedUrl'] = 'signedUrl';
                $context['expiresAtMessageKey'] = 'expiresAtMessageKey';
                $context['expiresAtMessageData'] = 'expiresAtMessageData';
                $this->assertArrayHasKey('signedUrl', $context);
                $this->assertArrayHasKey('expiresAtMessageKey', $context);
                $this->assertArrayHasKey('expiresAtMessageData', $context);
                return true;
            }));


        $this->emailVerifier->sendEmailConfirmation($routeName, $user, $email);
    }

    /**
     * @group email
     * @group email-verifier
     * @group email-verifier-handle-email-confirmation-request
     * @throws VerifyEmailExceptionInterface
     */
    public function testHandleEmailConfirmationRequest(): void
    {
        $user = $this->createMock(UserInterface::class);
        $user->method('getId')->willReturn(1);
        $user->method('getEmail')->willReturn('test@handle-email.fr');
        $user->method('isVerified')->willReturn(false);


        $request = $this->createMock(Request::class);
        $request->method('getUri')->willReturn('http://localhost:8000/verify/1/1');

        $this->verifyEmailHelper->expects($this->once())
            ->method('validateEmailConfirmation')
            ->with('http://localhost:8000/verify/1/1', 1, $user->getEmail());

        $this->emailVerifier->handleEmailConfirmationRequest($request, $user);
    }
}