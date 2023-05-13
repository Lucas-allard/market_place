<?php

namespace App\Controller;

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
        ProductService $productService,
        CategoryService $categoryService
    ): Response
    {
        $topProducts = $productService->getTopProductsWithCategories( 18);
        $bestCategories = $categoryService->getCategoriesHavingMostProductsAndBestProduct();
        return $this->render('home/index.html.twig', [
            'bestCategories' => $bestCategories,
            'topProducts' => $topProducts,
        ]);
    }
}
