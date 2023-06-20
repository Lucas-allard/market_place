<?php

namespace App\Service\Product;

use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Service\Pagination\PaginationService;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;

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

    /**
     * @throws Exception
     */
    public function getProductsByCategorySlug(
        string $categorySlug,
        ?string $subCategorySlug = null,
        ?string $order = null,
        ?int $page = null,
        ?int $limit = null
    ): array
    {
        if ($subCategorySlug) {
            $categorySlug = $subCategorySlug;
        }

        [$order, $page, $limit] = $this->setDefaultValues($order, $page, $limit);

        $query = $this->productRepository->getProductsByCategorySlugQuery($categorySlug, $order);

        return $this->paginationService->getPaginatedResult($query, $page, $limit);
    }

    /**
     * @throws Exception
     */
    public function getProductsByFilter(
        mixed $filterData,
        Category $category,
        ?Category $subCategory = null,
        ?string $order = null,
        ?int $page = null,
        ?int $limit = null
    ): array
    {
        if ($subCategory) {
            $category = $subCategory;
        }

        [$order, $page, $limit] = $this->setDefaultValues($order, $page, $limit);

        [$minPrice, $maxPrice, $brandArray, $caracteristicArray] = $this->extractFilterData($filterData);

        $queryBuilder = $this->productRepository->getProductsByFilterQuery(
            $category,
            $minPrice,
            $maxPrice,
            $brandArray,
            $caracteristicArray,
            $order
        );

        return $this->paginationService->getPaginatedResult($queryBuilder, $page, $limit);
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

    private function setDefaultValues(?string &$order, ?int &$page, ?int &$limit): array
    {
        $order = $order ?? 'DESC';
        $page = $page ?? 1;
        $limit = $limit ?? 16;

        return [$order, $page, $limit];
    }

    private function extractFilterData(mixed $filterData): array
    {
        $minPrice = $filterData['price']['min'] ?? null;
        $maxPrice = $filterData['price']['max'] ?? null;
        $filterBrand = $filterData['brand'] ?? null;
        $brandArray = $this->extractFilterArray($filterBrand);
        $filterCaracteristic = $filterData['caracteristic'] ?? null;
        $caracteristicArray = $this->extractFilterArray($filterCaracteristic);

        return [$minPrice, $maxPrice, $brandArray, $caracteristicArray];
    }

    private function extractFilterArray(?Collection $filterItems): array
    {
        $filterArray = [];

        if ($filterItems) {
            foreach ($filterItems as $filterItem) {
                $filterArray[] = $filterItem->getId();
            }
        }

        return $filterArray;
    }
}