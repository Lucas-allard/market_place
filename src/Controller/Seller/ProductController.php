<?php

namespace App\Controller\Seller;

use App\Entity\Product;
use App\Form\ProductForm\ProductFormType;
use App\Repository\BrandRepository;
use App\Service\Form\FormProcessor;
use App\Service\Product\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_SELLER')]
#[Route('/ma-boutique/produits', name: 'app_seller_product')]
class ProductController extends AbstractController
{
    /**
     * @var FormProcessor
     */
    private FormProcessor $formProcessor;
    /**
     * @var ProductService
     */
    private ProductService $productService;

    /**
     * @param FormProcessor $formProcessor
     * @param ProductService $productService
     */
    public function __construct(
        FormProcessor  $formProcessor,
        ProductService $productService,
    )
    {
        $this->formProcessor = $formProcessor;
        $this->productService = $productService;
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $parameters = $request->query->all();
        $sort = $parameters['sort'] ?? null;
        $page = $request->query->get('page');
        $limit = $request->query->get('limit');

        $products = $this->productService->getProductsBySeller($this->getUser(), $sort, $page, $limit);

        return $this->render('seller/product/index.html.twig', [
            'products' => $products['data'],
            'pagination' => $products['pagination'],
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/ajouter', name: '_add', methods: ['POST', 'GET'])]
    public function add(Request $request): Response
    {

        $productFactory = $this->productService->getProductFactory();

        $form = $this->createForm(ProductFormType::class, $productFactory->create());

        if ($this->formProcessor->process($request, $form)) {

            $this->addFlash('success', 'Le produit a bien été ajouté');

            return $this->redirectToRoute('app_seller_product_index');
        }

        return $this->render('seller/product/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    #[Route('/modifier/{slug}', name: '_edit', methods: ['POST', 'GET'])]
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->formProcessor->create(ProductFormType::class, $product, ['thumbnail' => true]);

        if ($this->formProcessor->process($request, $form)) {

            $this->addFlash('success', 'Le produit a bien été modifié');

            return $this->redirectToRoute('app_seller_product_index');
        }

        return $this->render('seller/product/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return JsonResponse
     */
    #[Route('/supprimer/{slug}', name: '_delete', methods: ['DELETE'])]
    public function delete(Request $request, Product $product): JsonResponse
    {
        $token = $request->headers->get('X-CSRF-TOKEN');

        if (!$this->isCsrfTokenValid('product-delete-' . $product->getId(), $token)) {
            return $this->json([
                'status' => 'error',
                'message' => 'Le token est invalide'
            ], Response::HTTP_FORBIDDEN);
        }

        $this->productService->delete($product);

        return $this->json([
            'status' => 'success',
            'message' => 'Le produit a bien été supprimé',
        ], Response::HTTP_OK);
    }

    /**
     * @param Product $product
     * @return Response
     */
    #[Route('/{slug}', name: '_show', methods: ['GET'])]
    public function show(Product $product): Response
    {

        return $this->render('seller/product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
