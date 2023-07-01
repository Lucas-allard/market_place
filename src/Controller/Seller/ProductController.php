<?php

namespace App\Controller\Seller;

use App\Form\ProductForm\ProductFormType;
use App\Service\Form\FormProcessor;
use App\Service\Product\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SELLER')]
#[Route('/ma-boutique/produits', name: 'app_seller_product')]
class ProductController extends AbstractController
{
    private FormProcessor $formProcessor;
    private ProductService $productService;

    public function __construct(
        FormProcessor  $formProcessor,
        ProductService $productService
    )
    {
        $this->formProcessor = $formProcessor;
        $this->productService = $productService;
    }

    #[Route('/', name: '_index')]
    public function index(): Response
    {
        $products = $this->productService->getProductsBySeller($this->getUser());

        return $this->render('seller/product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/ajouter', name: '_add')]
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

    #[Route('/modifier/{slug}', name: '_edit')]
    public function edit(): Response
    {
        return $this->render('seller/product/index.html.twig', [
            'controller_name' => 'ProductsController',
        ]);
    }

    #[Route('/supprimer/{slug}', name: '_delete')]
    public function delete(): Response
    {
        return $this->render('seller/product/index.html.twig', [
            'controller_name' => 'ProductsController',
        ]);
    }

    #[Route('/{slug}', name: '_show')]
    public function show(): Response
    {
        return $this->render('seller/product/index.html.twig', [
            'controller_name' => 'ProductsController',
        ]);
    }
}
