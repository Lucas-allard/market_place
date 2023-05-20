<?php

namespace App\Controller;

use App\Service\Brand\BrandService;
use App\Service\Category\CategoryService;
use App\Service\Product\ProductService;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/', name: 'app_home')]
    public function index(
        ProductService  $productService,
        CategoryService $categoryService,
        BrandService    $brandService
    ): Response
    {

        return $this->render('home/index.html.twig', [
            'newProducts' => $productService->getNewsArrivalsProducts(4),
            'discountProducts' => $productService->getSellsProductsHasDiscount(10),
            'brands' => $brandService->getBrandsWithPictures(),
            'topBrands' => $brandService->getTopBrands(16),
            'bestCategories' => $categoryService->getCategoriesHavingMostProductsAndBestProduct(),
            'topProducts' => $productService->getTopProductsOrdered(18),
        ]);
    }
}
