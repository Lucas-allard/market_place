<?php

namespace App\Controller\Admin;

use App\Entity\Brand;
use App\Form\BrandForm\BrandFormType;
use App\Service\Brand\BrandService;
use App\Service\Form\FormProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/marques', name: 'app_admin_brand')]
#[IsGranted('ROLE_ADMIN')]
class BrandController extends AbstractController
{
    /**
     * @var BrandService
     */
    private BrandService $brandService;
    /**
     * @var FormProcessor
     */
    private FormProcessor $formProcessor;

    /**
     * @param BrandService $brandService
     * @param FormProcessor $formProcessor
     */
    public function __construct(BrandService $brandService, FormProcessor $formProcessor)
    {
        $this->brandService = $brandService;
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

        $brands = $this->brandService->getPaginatedBrands($sort, $page, $limit);

        return $this->render('admin/brand/index.html.twig', [
            'brands' => $brands['data'],
            'pagination' => $brands['pagination'],
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/ajouter', name: '_create')]
    public function add(Request $request): Response
    {
        $brandFactory = $this->brandService->getFactory();

        $form = $this->formProcessor->create(BrandFormType::class, $brandFactory->create());

        if ($this->formProcessor->process($request, $form)) {

            $this->addFlash('success', 'La marque a bien été ajoutée.');

            return $this->redirectToRoute('app_admin_brand_index');
        }

        return $this->render('admin/brand/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Brand $brand
     * @return Response
     */
    #[Route('/{id}/modifier', name: '_edit')]
    public function edit(Request $request, Brand $brand): Response
    {
        $form = $this->formProcessor->create(BrandFormType::class, $brand,  ['thumbnail' => true]);

        if ($this->formProcessor->process($request, $form)) {

            $this->addFlash('success', 'La marque a bien été modifiée.');

            return $this->redirectToRoute('app_admin_brand_index');
        }

        return $this->render('admin/brand/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Brand $brand
     * @return JsonResponse
     */
    #[Route('/{id}/supprimer', name: '_delete')]
    public function delete(Request $request, Brand $brand): JsonResponse
    {
        $token = $request->headers->get('X-CSRF-Token');

        if (!$this->isCsrfTokenValid('brand-delete-' . $brand->getId(), $token)) {
            return $this->json([
                'status' => 'error',
                'message' => 'Le token est invalide.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->brandService->deleteBrand($brand);

        return $this->json([
            'status' => 'success',
            'message' => 'La marque a bien été supprimée.'
        ], Response::HTTP_OK);
    }
}
