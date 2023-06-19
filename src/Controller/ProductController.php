<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\FilterForm\FilterFormType;
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
    private ProductService $productService;
    private FormProcessor $formProcessor;

    public function __construct(ProductService $productService, FormProcessor $formProcessor)
    {
        $this->productService = $productService;
        $this->formProcessor = $formProcessor;
    }

    /**
     * @param Request $request
     * @param Category $category
     * @param Category|null $subCategory
     * @return Response
     */
    #[Route('/{categorySlug}/{subCategorySlug?}', name: '_index', requirements: [
        'categorySlug' => '[a-zA-Z0-9-_]+',
        'subCategorySlug' => '[a-zA-Z0-9-_]+'
    ])]
    #[ParamConverter('category', options: ['mapping' => ['categorySlug' => 'slug']])]
    #[ParamConverter('subCategory', options: ['mapping' => ['subCategorySlug' => 'slug']])]
    public function index(
        Request   $request,
        Category  $category,
        ?Category $subCategory = null
    ): Response
    {
        $order = $request->query->get('order');
        $page = $request->query->get('page');
        $limit = $request->query->get('limit');
        $products = $this->productService->getProductsByCategorySlug($category, $subCategory, $order, $page, $limit);

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
}
