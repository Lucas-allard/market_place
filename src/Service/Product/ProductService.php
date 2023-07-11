<?php

namespace App\Service\Product;

use App\Entity\Category;
use App\Entity\Product;
use App\Factory\ProductFactory;
use App\Repository\ProductRepository;
use App\Service\Pagination\PaginationService;
use App\Service\SortableInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductService implements SortableInterface
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
     * @var ProductFactory
     */
    private ProductFactory $productFactory;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param ProductRepository $productRepository
     * @param PaginationService $paginationService
     * @param ProductFactory $productFactory
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        ProductRepository $productRepository,
        PaginationService $paginationService,
        ProductFactory $productFactory,
        EntityManagerInterface $entityManager
    )
    {
        $this->productRepository = $productRepository;
        $this->paginationService = $paginationService;
        $this->productFactory = $productFactory;
        $this->entityManager = $entityManager;
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
    public function getAllProducts(): array
    {
        return $this->productRepository->findAll();
    }

    /**
     * @param int $id
     * @return object|Product|null
     */
    public function getProductById(int $id): object
    {
        return $this->productRepository->find($id);
    }

    /**
     * @param string $slug
     * @return object|Product|null
     */
    public function getProductBySlug(string $slug): object
    {
        return $this->productRepository->findOneBy(['slug' => $slug]);
    }

    /**
     * @param int $maxResults
     * @param int|null $offset
     * @return array
     */
    public function getNewsArrivalsProducts(int $maxResults, ?int $offset = null): array
    {
        return $this->productRepository->findNewsArrivalsProducts($maxResults, $offset);
    }

    /**
     * @param int $maxResults
     * @return array
     */
    public function getTopProductsOrdered(int $maxResults): array
    {
        return $this->productRepository->findTopProductsOrdered($maxResults);
    }

    /**
     * @param int $maxResults
     * @return array
     */
    public function getSellsProductsHasDiscount(int $maxResults): array
    {
        return $this->productRepository->findSellsProductsHasDiscount($maxResults);
    }

    /**
     * @param array $categoryIds
     * @return float|int|mixed|string
     */
    public function getBestProductsByCategoryIds(array $categoryIds)
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
        ?string $order = null,
        ?int    $page = null,
        ?int    $limit = null
    ): array
    {


        [$order, $page, $limit] = $this->setDefaultValues($order, $page, $limit);

        $query =  $subCategory ?
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

        [$order, $page, $limit] = $this->setDefaultValues($order, $page, $limit);

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
        ?array $sort = null,
        ?int $page = null,
        ?int $limit = null
    ): array
    {
        [$sort, $order] = $this->getOrderBy($sort);

        [$order, $page, $limit] = $this->setDefaultValues($order, $page, $limit);

        $queryBuilder = $this->productRepository->getProductsBySellerQuery($user, $sort, $order);

        return $this->paginationService->getPaginatedResult($queryBuilder, $page, $limit);
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
     * @param array|null $criteria
     * @return array|string[]
     */
    public function getOrderBy(?array $criteria): array
    {
        if ($criteria) {
            // the key is the field name and the value is the order
            $sort = array_key_first($criteria);
            $order = $criteria[$sort];

            return [$sort, $order];
        }

        return ['createdAt', 'DESC'];
    }

    /**
     * @param string|null $order
     * @param int|null $page
     * @param int|null $limit
     * @return array
     */
    public function setDefaultValues(?string &$order, ?int &$page, ?int &$limit): array
    {
        $order = $order ?? 'DESC';
        $page = $page ?? 1;
        $limit = $limit ?? 16;

        return [$order, $page, $limit];
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

}