<?php

namespace App\Controller\Admin;

use App\Entity\Interface\UserInterface;
use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/utilisateurs', name: 'app_admin_user')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    /**
     * @var UserService
     */
    private UserService $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/', name: '_index')]
    public function index(Request $request): Response
    {
        $parameters = $request->query->all();
        $sort = $parameters['sort'] ?? null;
        $page = $request->query->get('page');
        $limit = $request->query->get('limit');

        $users = $this->userService->getPaginatedUsers($sort, $page, $limit);

        return $this->render('admin/user/index.html.twig', [
            'users' => $users['data'],
            'pagination' => $users['pagination'],

        ]);
    }

    /**
     * @param Request $request
     * @param UserInterface $user
     * @return JsonResponse
     */
    #[Route('/supprimer/{id}', name: '_delete')]
    public function delete(Request $request, UserInterface $user): JsonResponse
    {
        $token = $request->headers->get('X-CSRF-Token');

        if (!$this->isCsrfTokenValid('user-delete-' . $user->getId(), $token)) {
            return $this->json([
                'status' => 'error',
                'message' => 'Le token est invalide'
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->userService->deleteUser($user);

        return $this->json([
            'status' => 'success',
            'message' => 'L\'utilisateur a bien été supprimé'
        ], Response::HTTP_OK);
    }
}
