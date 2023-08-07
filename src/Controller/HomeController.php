<?php

namespace App\Controller;

use App\Service\Brand\BrandService;
use App\Service\Category\CategoryService;
use App\Service\Product\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;


class HomeController extends AbstractController
{
    /**
     * @param ProductService $productService
     * @param CategoryService $categoryService
     * @param BrandService $brandService
     * @return Response
     */
    #[Route('/', name: 'app_home')]
    public function index(
        ProductService  $productService,
        CategoryService $categoryService,
        BrandService    $brandService,
        CacheInterface $cache
    ): Response
    {
        $newProducts = $cache->get('get_new_arrivals_products', function (ItemInterface $item) use ($productService) {
            $item->expiresAfter(3600);

            return $productService->getNewsArrivalsProducts(4);
        });

        $discountProducts = $cache->get('get_sells_products_has_discount', function (ItemInterface $item) use ($productService) {
            $item->expiresAfter(3600);

            return $productService->getSellsProductsHasDiscount(10);
        });

        $brands = $cache->get('get_brands_with_pictures', function (ItemInterface $item) use ($brandService) {
            $item->expiresAfter(3600);

            return $brandService->getBrandsWithPictures();
        });

        $topBrands = $cache->get('get_top_brands', function (ItemInterface $item) use ($brandService) {
            $item->expiresAfter(3600);

            return $brandService->getTopBrands(20);
        });

        $bestCategories = $cache->get('get_best_categories', function (ItemInterface $item) use ($categoryService) {
            $item->expiresAfter(3600);

            return $categoryService->getBestCategories();
        });

        $topProducts = $cache->get('get_top_products_ordered', function (ItemInterface $item) use ($productService) {
            $item->expiresAfter(3600);

            return $productService->getTopProductsOrdered(20);
        });


        return $this->render('home/index.html.twig', [
            'newProducts' => $newProducts,
            'discountProducts' => $discountProducts,
            'brands' => $brands,
            'topBrands' => $topBrands,
            'bestCategories' => $bestCategories,
            'topProducts' => $topProducts,
        ]);
    }
}
