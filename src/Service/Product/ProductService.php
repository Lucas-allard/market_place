<?php

namespace App\Service\Product;

use App\Entity\Category;
use App\Entity\Product;
use App\Factory\ProductFactory;
use App\Repository\ProductRepository;
use App\Service\Category\CategoryService;
use App\Service\Chart\ChartService;
use App\Service\Pagination\PaginationService;
use App\Service\Utils\SortHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\PersistentCollection;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ProductService
{
    /**
     * @var ProductRepository
     */
    private ProductRepository $productRepository;
    /**
     * @var PaginationService
     */
    private PaginationService $paginationService;

    /**
     * @var ChartService
     */
    private ChartService $chartService;

    /**
     * @var ProductFactory
     */
    private ProductFactory $productFactory;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @var SortHelper
     */
    private SortHelper $sortHelper;

    /**
     * @param ProductRepository $productRepository
     * @param PaginationService $paginationService
     * @param ChartService $chartService
     * @param ProductFactory $productFactory
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     * @param SortHelper $sortHelper
     */
    public function __construct(
        ProductRepository      $productRepository,
        PaginationService      $paginationService,
        ChartService           $chartService,
        ProductFactory         $productFactory,
        EntityManagerInterface $entityManager,
        TranslatorInterface    $translator,
        SortHelper             $sortHelper,
    )
    {
        $this->productRepository = $productRepository;
        $this->paginationService = $paginationService;
        $this->chartService = $chartService;
        $this->productFactory = $productFactory;
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->sortHelper = $sortHelper;
    }

    /**
     * @return int
     */
    public function getMinPrice(): int
    {
        try {
            return $this->productRepository->findMinPrice();
        } catch (NoResultException|NonUniqueResultException) {
            return 0;
        }
    }

    /**
     * @return int
     */
    public function getMaxPrice(): int
    {
        try {
            return $this->productRepository->findMaxPrice();
        } catch (NoResultException|NonUniqueResultException) {
            return 0;
        }
    }

    /**
     * @return array
     */
    public function getProducts(): array
    {
        return $this->productRepository->findBy([], ['createdAt' => 'DESC']);
    }

    /**
     * @param int $maxResults
     * @param int|null $offset
     * @return array
     */
    public function getNewsArrivalsProducts(int $maxResults, ?int $offset = null): array
    {
        $products = $this->productRepository->findNewsArrivalsProducts($maxResults, $offset);

        $this->preLoadAssociatedEntities($products);

        return $products;
    }

    /**
     * @param int $maxResults
     * @return array
     */
    public function getTopProductsOrdered(int $maxResults): array
    {
        $products = $this->productRepository->findTopProductsOrdered($maxResults);

        $this->preLoadAssociatedEntities($products);

        return $products;
    }

    /**
     * @param int $maxResults
     * @return array
     */
    public function getSellsProductsHasDiscount(int $maxResults): array
    {
        $products = $this->productRepository->findSellsProductsHasDiscount($maxResults);

        $this->preLoadAssociatedEntities($products);

        return $products;
    }

    /**
     * @param array $categoryIds
     * @return array
     */
    public function getBestProductsByCategoryIds(array $categoryIds): array
    {
        return $this->productRepository->findBestProductsByCategoryIds($categoryIds);
    }

    /**
     * @param Category $category
     * @param Category|null $subCategory
     * @param string|null $order
     * @param int|null $page
     * @param int|null $limit
     * @return array
     */
    public function getProductsByCategory(
        Category  $category,
        ?Category $subCategory = null,
        ?string   $order = null,
        ?int      $page = null,
        ?int      $limit = null
    ): array
    {


        [$order, $page, $limit] = $this->sortHelper->setDefaultValues($order, $page, $limit);

        $query = $subCategory ?
            $this->productRepository->getProductsByCategoryIdQuery($subCategory->getId(), $category->getId(), $order) :
            $this->productRepository->getProductsByCategoryIdQuery($category->getId(), null, $order);

        return $this->paginationService->getPaginatedResult($query, $page, $limit);
    }

    /**
     * @param mixed $filterData
     * @param Category $category
     * @param Category|null $subCategory
     * @param string|null $order
     * @param int|null $page
     * @param int|null $limit
     * @return array
     */
    public function getProductsByFilter(
        mixed     $filterData,
        Category  $category,
        ?Category $subCategory = null,
        ?string   $order = null,
        ?int      $page = null,
        ?int      $limit = null
    ): array
    {

        [$order, $page, $limit] = $this->sortHelper->setDefaultValues($order, $page, $limit);

        [$minPrice, $maxPrice, $brandArray, $caracteristicArray] = $this->extractFilterData($filterData);

        $query = $subCategory ?
            $this->productRepository->getProductsByFilterQuery(
                $subCategory->getId(),
                $category->getId(),
                $minPrice,
                $maxPrice,
                $brandArray,
                $caracteristicArray,
                $order
            ) :
            $this->productRepository->getProductsByFilterQuery(
                $category->getId(),
                null,
                $minPrice,
                $maxPrice,
                $brandArray,
                $caracteristicArray,
                $order
            );

        return $this->paginationService->getPaginatedResult($query, $page, $limit);
    }

    /**
     * @param UserInterface|null $user
     * @param array|null $sort
     * @param int|null $page
     * @param int|null $limit
     * @return array
     */
    public function getProductsBySeller(
        ?UserInterface $user,
        ?array         $sort = null,
        ?int           $page = null,
        ?int           $limit = null
    ): array
    {
        [$sort, $order] = $this->sortHelper->getOrderBy($sort);

        [$order, $page, $limit] = $this->sortHelper->setDefaultValues($order, $page, $limit);

        $queryBuilder = $this->productRepository->getProductsBySellerQuery($user, $sort, $order);

        return $this->paginationService->getPaginatedResult($queryBuilder, $page, $limit);
    }


    /**
     * @param array $products
     * @return Chart
     */
    public function getProductsPerMonthChart(array $products): Chart
    {
        $total = [];

        foreach ($products as $product) {
            $month = $product->getCreatedAt()->format('F');
            $monthName = $this->translator->trans($month);

            if (!isset($total[$monthName])) {
                $total[$monthName] = 0;
            }

            $total[$monthName] += $product->getQuantity();
        }

        return $this->chartService->buildChart($total, Chart::TYPE_BAR, true);
    }


    /**
     * @param array $products
     * @return Chart
     */
    public function getProductsPerCategoryChart(array $products): Chart
    {
        $categoryTotals = [];
        $productIds = array_map(fn($product) => $product->getId(), $products);
        $categoryRepository = $this->entityManager->getRepository(Category::class);
        $results = $categoryRepository->findTotalProductPerCategories($productIds);

        foreach ($results as $result) {
            $category = $result['category'];
            $total = $result['total'];
            $categoryTotals[$category] = $total;
        }

        return $this->chartService->buildChart($categoryTotals, Chart::TYPE_BAR, true);
    }


    /**
     * @param array $products
     * @return Chart
     */
    public function getProductsPerSellerChart(array $products): Chart
    {
        $total = [];

        foreach ($products as $product) {
            $seller = $product->getSeller()->getCompany();

            if (!isset($total[$seller])) {
                $total[$seller] = 0;
            }

            $total[$seller]++;
        }

        return $this->chartService->buildChart($total, Chart::TYPE_BAR, true);

    }

    /**
     * @param Product $product
     * @return void
     */
    public function delete(Product $product): void
    {
        $this->productRepository->remove($product, true);
    }

    /**
     * @return ProductFactory
     */
    public function getProductFactory(): ProductFactory
    {
        return $this->productFactory;
    }

    /**
     * @param mixed $filterData
     * @return array
     */
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

    /**
     * @param Collection|null $filterItems
     * @return array
     */
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

    /**
     * @param array $products
     * @return void
     */
    public function preLoadAssociatedEntities(array $products): void
    {
        foreach ($products as $product) {
            $categories = $product->getCategories();
            $caracteristics = $product->getCaracteristics();
            $pictures = $product->getPictures();

            // Vérification et conversion des collections d'entités en ArrayCollection
            if ($categories instanceof PersistentCollection) {
                $categories = new ArrayCollection($categories->getValues());
            }

            if ($caracteristics instanceof PersistentCollection) {
                $caracteristics = new ArrayCollection($caracteristics->getValues());
            }

            if ($pictures instanceof PersistentCollection) {
                $pictures = new ArrayCollection($pictures->getValues());
            }

            $product->setCategories($categories)
                ->setCaracteristics($caracteristics)
                ->setPictures($pictures);
        }
    }
}