<?php

namespace App\Controller;

use App\Entity\Interface\UserInterface;
use App\Entity\User;
use App\Form\SecurityForm\ForgottenPasswordRequestFormType;
use App\Form\SecurityForm\ResetPasswordFormType;
use App\Repository\UserRepository;
use App\Service\Form\FormProcessor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

#[Route('/mot-de-passe-oublie', name: 'app_forgot_password')]
class ResetPasswordRequestController extends AbstractController
{

    /**
     * @var FormProcessor
     */
    private FormProcessor $formProcessor;

    /**
     * @param FormProcessor $formProcessor
     */
    public function __construct(FormProcessor $formProcessor)
    {
        $this->formProcessor = $formProcessor;
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/', name: '_request')]
    public function forgottenPassword(
        Request                 $request,
        UserRepository          $userRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface  $entityManager,
        MailerInterface         $mailer
    ): Response
    {
        $requestForm = $this->createForm(ForgottenPasswordRequestFormType::class);
        $requestForm->handleRequest($request);

        if ($requestForm->isSubmitted() && $requestForm->isValid()) {
            $user = $userRepository->findOneBy(['email' => $requestForm->get('email')->getData()]);
            if ($user) {
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();

                $url = $this->generateUrl('app_forgot_password_reset', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $message = (new TemplatedEmail())
                    ->from($this->getParameter('from_email'))
                    ->to($user->getEmail())
                    ->subject('Demande de réinitialisation de mot de passe')
                    ->htmlTemplate('email/forgotten_password.html.twig')
                    ->context([
                        'url' => $url,
                        'user' => $user,
                    ]);


                $mailer->send($message);

                $this->addFlash('success', 'Un email de réinitialisation de mot de passe vous a été envoyé');

                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('danger', 'Vous ne possédez pas de compte');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password_request/forgotten_password_request.html.twig', [
            'requestForm' => $requestForm->createView(),
        ]);
    }

    /**
     * @param string $token
     * @param Request $request
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     */
    #[Route('/verification/{token}', name: '_reset')]
    public function resetPassword(
        string                      $token,
        Request                     $request,
        UserRepository              $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $user = $userRepository->findOneBy(['resetToken' => $token]);
        if (!$user) {
            $this->addFlash('danger', 'Token Inconnu');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->getResetForm($user);

        $this->formProcessor->handleRequest($request, $form);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->updatePassword($user, $passwordHasher->hashPassword($user, $form->get('plainPassword')->getData()));
            $this->formProcessor->save($user);

            $this->addFlash('success', 'Mot de passe mis à jour');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password_request/reset_password.html.twig', [
            'token' => $token,
            'resetForm' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     */
    #[Route('/reinitialisation', name: '_reset_on_logged')]
    public function resetPasswordOnLogged(
        Request                     $request,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $form = $this->getResetForm($this->getUser());

        $this->formProcessor->handleRequest($request, $form);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->updatePassword($this->getUser(), $passwordHasher->hashPassword($this->getUser(), $form->get('password')->getData()));
            $this->formProcessor->save($this->getUser());

            $this->addFlash('success', 'Mot de passe mis à jour');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/security/reset_password.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }


    /**
     * @param User $user
     * @return FormInterface
     */
    public function getResetForm(UserInterface $user): FormInterface
    {
        return $this->formProcessor->create(ResetPasswordFormType::class, $user);
    }

    /**
     * @param UserInterface $user
     * @param string $password
     * @return void
     */
    public function updatePassword(UserInterface $user, string $password): void
    {
        $user->setResetToken(null);
        $user->setPassword($password);
    }
}
