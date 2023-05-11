<?php

namespace App\Controller;

use App\Service\Product\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductService $productService): Response
    {
        $topProducts = $productService->getTopProductsWithCategories(true, 18);

        return $this->render('home/index.html.twig', [
            'topProducts' => $topProducts,
        ]);
    }
}
