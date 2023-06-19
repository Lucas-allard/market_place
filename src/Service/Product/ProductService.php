<?php

namespace App\Service\Product;

use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Service\Pagination\PaginationService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ProductService
{
    private ProductRepository $productRepository;
    private PaginationService $paginationService;


    public function __construct(ProductRepository $productRepository, PaginationService $paginationService)
    {
        $this->productRepository = $productRepository;
        $this->paginationService = $paginationService;
    }

    public function getAllProducts(): array
    {
        return $this->productRepository->findAll();
    }

    public function getProductById(int $id): object
    {
        return $this->productRepository->find($id);
    }

    public function getProductBySlug(string $slug): object
    {
        return $this->productRepository->findOneBy(['slug' => $slug]);
    }


    public function getNewsArrivalsProducts(int $maxResults, ?int $offset = null): array
    {
        return $this->productRepository->findNewsArrivalsProducts($maxResults, $offset);
    }

    public function getTopProductsOrdered(int $maxResults): array
    {
        return $this->productRepository->findTopProductsOrdered($maxResults);
    }

    public function getSellsProductsHasDiscount(int $maxResults): array
    {
        return $this->productRepository->findSellsProductsHasDiscount($maxResults);
    }

    public function getProductsByCategorySlug(
        string  $categorySlug,
        ?string $subCategorySlug = null,
        ?string $order = null,
        ?int    $page = null,
        ?int    $limit = null
    ): array
    {
        if ($subCategorySlug) {
            $categorySlug = $subCategorySlug;
        }

        if (!$order) {
            $order = 'DESC';
        }

        if (!$page) {
            $page = 1;
        }

        if (!$limit) {
            $limit = 16;
        }


        $queryBuilder = $this->productRepository->getProductsByCategorySlugQueryBuilder($categorySlug, $order, $page, $limit);
        $queryBuilder->setFirstResult(($page * $limit) - $limit);
        $queryBuilder->setMaxResults($limit);

        $this->paginationService->setQueryBuilder($queryBuilder);
        $this->paginationService->setCurrentPage($page);
        $this->paginationService->setLimit($limit);

        return $this->paginationService->getPaginatedResult();
    }

    public function getMinPrice(): int
    {
        try {
            return $this->productRepository->findMinPrice();
        } catch (NoResultException|NonUniqueResultException) {
            return 0;
        }
    }

    public function getMaxPrice(): int
    {
        try {
            return $this->productRepository->findMaxPrice();
        } catch (NoResultException|NonUniqueResultException) {
            return 0;
        }
    }

    public function getProductsByFilter(
        mixed $filterData,
        Category $category,
        ?Category $subCategory = null,
        ?string $order = null,
        ?int    $page = null,
        ?int    $limit = null
    ): array
    {
        if (!$subCategory) {
            $subCategory = $category;
        }

        if (!$order) {
            $order = 'DESC';
        }

        if (!$page) {
            $page = 1;
        }

        if (!$limit) {
            $limit = 16;
        }

        $minPrice = $filterData['price']['min'] ?? null;
        $maxPrice = $filterData['price']['max'] ?? null;
        $filterBrand = $filterData['brand'] ?? null;
        $brandArray = [];
        foreach ($filterBrand as $brand) {
            $brandArray[] = $brand->getId();
        }
        $filterCaracteristic = $filterData['caracteristic'] ?? null;
        $caracteristicArray = [];
        foreach ($filterCaracteristic as $caracteristic) {
            $caracteristicArray[] = $caracteristic->getId();
        }

        $queryBuilder = $this->productRepository->getProductsByFilterQueryBuilder(
            $subCategory,
            $minPrice,
            $maxPrice,
            $brandArray,
            $caracteristicArray,
            $order
        );

        $queryBuilder->setFirstResult(($page * $limit) - $limit);
        $queryBuilder->setMaxResults($limit);

        $this->paginationService->setQueryBuilder($queryBuilder);
        $this->paginationService->setCurrentPage($page);
        $this->paginationService->setLimit($limit);

        return $this->paginationService->getPaginatedResult();
    }
}