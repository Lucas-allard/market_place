<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryForm\CategoryFormType;
use App\Service\Category\CategoryService;
use App\Service\Form\FormProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/categories', name: 'app_admin_category')]
#[IsGranted('ROLE_ADMIN')]
class CategoryController extends AbstractController
{
    /**
     * @var CategoryService
     */
    private CategoryService $categoryService;

    /**
     * @var FormProcessor
     */
    private FormProcessor $formProcessor;

    /**
     * @param CategoryService $categoryService
     * @param FormProcessor $formProcessor
     */
    public function __construct(
        CategoryService $categoryService,
        FormProcessor   $formProcessor
    )
    {
        $this->categoryService = $categoryService;
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

        $categories = $this->categoryService->getPaginatedCategories($sort, $page, $limit);

        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories['data'],
            'pagination' => $categories['pagination'],
        ]);

    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/ajouter', name: '_create')]
    public function create(Request $request): Response
    {

        $categoryFactory = $this->categoryService->getCategoryFactory();

        $form = $this->formProcessor->create(CategoryFormType::class, $categoryFactory->create());

        if ($this->formProcessor->process($request, $form)) {

            $this->addFlash('success', 'La catégorie a bien été ajoutée');

            return $this->redirectToRoute('app_admin_category_index');
        }

        return $this->render('admin/category/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Category $category
     * @return Response
     */
    #[Route('/modifier/{slug}', name: '_edit')]
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->formProcessor->create(CategoryFormType::class, $category);

        if ($this->formProcessor->process($request, $form)) {

            $this->addFlash('success', 'La catégorie a bien été modifiée');

            return $this->redirectToRoute('app_admin_category_index');
        }

        return $this->render('admin/category/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Category $category
     * @return JsonResponse
     */
    #[Route('/supprimer/{id}', name: '_delete')]
    public function delete(Request $request, Category $category): JsonResponse
    {
        $token = $request->headers->get('X-CSRF-Token');

        if (!$this->isCsrfTokenValid('category-delete-' . $category->getId(), $token)) {
            return $this->json([
                'status' => 'error',
                'message' => 'Le token est invalide'
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->categoryService->deleteCategory($category);

        return $this->json([
            'status' => 'success',
            'message' => 'L\'utilisateur a bien été supprimé'
        ], Response::HTTP_OK);
    }
}
