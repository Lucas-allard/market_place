<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\FilterForm\FilterFormType;
use App\Service\Category\CategoryService;
use App\Service\Form\FormProcessor;
use App\Service\Product\ProductService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/produits', name: 'app_product')]
class ProductController extends AbstractController
{
    /**
     * @var ProductService
     */
    private ProductService $productService;
    /**
     * @var CategoryService
     */
    private CategoryService $categoryService;
    /**
     * @var FormProcessor
     */
    private FormProcessor $formProcessor;

    /**
     * @param ProductService $productService
     * @param CategoryService $categoryService
     * @param FormProcessor $formProcessor
     */
    public function __construct(ProductService $productService, CategoryService $categoryService, FormProcessor $formProcessor)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->formProcessor = $formProcessor;
    }

    /**
     * @param Request $request
     * @param Category $category
     * @param string|null $subCategorySlug
     * @return Response
     */
    #[Route('/{categorySlug}/{subCategorySlug?}', name: '_index', requirements: [
        'categorySlug' => '[a-zA-Z0-9-_]+',
        'subCategorySlug' => '[a-zA-Z0-9-_]+'
    ])]
    #[ParamConverter('category', options: ['mapping' => ['categorySlug' => 'slug']])]
    public function index(
        Request $request,
        Category $category,
        ?string $subCategorySlug = null
    ): Response {
        $order = $request->query->get('order');
        $page = $request->query->get('page');
        $limit = $request->query->get('limit');

        $subCategory = null;
        if ($subCategorySlug !== null) {
            // Récupérer la sous-catégorie en fonction du slug et de la catégorie parente
            $subCategory = $this->categoryService->getSubCategoryBySlug($subCategorySlug, $category);
        }

        $products = $this->productService->getProductsByCategory($category, $subCategory, $order, $page, $limit);

        $minPrice = $this->productService->getMinPrice();
        $maxPrice = $this->productService->getMaxPrice();

        $filterForm = $this->formProcessor->create(FilterFormType::class, null, [
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'category' => $subCategory !== null ? $subCategory : $category,
        ]);

        $this->formProcessor->handleRequest($request, $filterForm);

        if ($this->formProcessor->isValid($filterForm)) {
            $products = $this->productService->getProductsByFilter($filterForm->getData(), $category, $subCategory, $order, $page, $limit);
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'category' => $category,
            'subCategory' => $subCategory,
            'filterForm' => $filterForm->createView(),
        ]);
    }

    /**
     * @param Category $category
     * @param Category $subCategory
     * @param Product $product
     * @return Response
     */
    #[Route('/{categorySlug}/{subCategorySlug}/{productSlug}', name: '_show', requirements: [
        'categorySlug' => '[a-zA-Z0-9-_]+',
        'subCategorySlug' => '[a-zA-Z0-9-_]+',
        'productSlug' => '[a-zA-Z0-9-_]+',
    ])]
    #[ParamConverter('category', options: ['mapping' => ['categorySlug' => 'slug']])]
    #[ParamConverter('subCategory', options: ['mapping' => ['subCategorySlug' => 'slug']])]
    #[ParamConverter('product', options: ['mapping' => ['productSlug' => 'slug']])]
    public function show(
        Category $category,
        Category $subCategory,
        Product   $product
    ): Response
    {
        return $this->render('product/show.html.twig', [
            'category' => $category,
            'subCategory' => $subCategory,
            'product' => $product,
        ]);
    }
}
