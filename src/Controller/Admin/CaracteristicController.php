<?php

namespace App\Controller\Admin;

use App\Entity\Caracteristic;
use App\Form\CaracteristicForm\CaracteristicFormType;
use App\Service\Caracteristic\CaracteristicService;
use App\Service\Form\FormProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/caracteristiques', name: 'app_admin_caracteristic')]
#[IsGranted('ROLE_ADMIN')]
class CaracteristicController extends AbstractController
{
    /**
     * @var CaracteristicService
     */
    private CaracteristicService $caracteristicService;
    /**
     * @var FormProcessor
     */
    private FormProcessor $formProcessor;

    /**
     * @param CaracteristicService $caracteristicService
     * @param FormProcessor $formProcessor
     */
    public function __construct(
        CaracteristicService $caracteristicService,
        FormProcessor        $formProcessor
    )
    {
        $this->caracteristicService = $caracteristicService;
        $this->formProcessor = $formProcessor;
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

        $caracteristics = $this->caracteristicService->getPaginatedCaracteristics($sort, $page, $limit);

        return $this->render('admin/caracteristic/index.html.twig', [
            'caracteristics' => $caracteristics['data'],
            'pagination' => $caracteristics['pagination'],
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/ajouter', name: '_create')]
    public function create(Request $request): Response
    {
        $factory = $this->caracteristicService->getFactory();

        $form = $this->formProcessor->create(CaracteristicFormType::class, $factory->create());

        if ($this->formProcessor->process($request, $form)) {

            $this->addFlash('success', 'La caractéristique a été ajoutée avec succès.');

            return $this->redirectToRoute('app_admin_caracteristic_index');
        }

        return $this->render('admin/caracteristic/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Caracteristic $caracteristic
     * @return Response
     */
    #[Route('/{id}/modifier', name: '_edit')]
    public function edit(Request $request, Caracteristic $caracteristic): Response
    {
        $form = $this->formProcessor->create(CaracteristicFormType::class, $caracteristic);

        if ($this->formProcessor->process($request, $form)) {

            $this->addFlash('success', 'La caractéristique a été modifiée avec succès.');

            return $this->redirectToRoute('app_admin_caracteristic_index');
        }

        return $this->render('admin/caracteristic/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Caracteristic $caracteristic
     * @return JsonResponse
     */
    #[Route('/{id}/supprimer', name: '_delete')]
    public function delete(Request $request, Caracteristic $caracteristic): JsonResponse
    {
        $token = $request->headers->get('X-CSRF-Token');

        if (!$this->isCsrfTokenValid('caracteristic-delete-' . $caracteristic->getId(), $token)) {
            return $this->json([
                'status' => 'error',
                'message' => 'Token CSRF invalide.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->caracteristicService->deleteCaracteristic($caracteristic);

        return $this->json([
            'status' => 'success',
            'message' => 'La caractéristique a été supprimée avec succès.'
        ], Response::HTTP_OK);
    }
}
