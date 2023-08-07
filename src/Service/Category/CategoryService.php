<?php

namespace App\Service\Category;

use App\Entity\Category;
use App\Factory\CategoryFactory;
use App\Repository\CategoryRepository;
use App\Service\Pagination\PaginationService;
use App\Service\Product\ProductService;
use App\Service\Utils\SortHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

class CategoryService
{
    /**
     * @var CategoryRepository
     */
    private CategoryRepository $categoryRepository;

    /**
     * @var ProductService
     */
    private ProductService $productService;

    /**
     * @var PaginationService
     */
    private PaginationService $paginationService;

    /**
     * @var SortHelper
     */
    private SortHelper $sortHelper;

    /**
     * @var CategoryFactory
     */
    private CategoryFactory $categoryFactory;

    /**
     * @param CategoryRepository $categoryRepository
     * @param ProductService $productService
     * @param PaginationService $paginationService
     * @param SortHelper $sortHelper
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        ProductService     $productService,
        PaginationService  $paginationService,
        SortHelper         $sortHelper,
        CategoryFactory    $categoryFactory,
    )
    {
        $this->categoryRepository = $categoryRepository;
        $this->productService = $productService;
        $this->paginationService = $paginationService;
        $this->sortHelper = $sortHelper;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @param int $id
     * @return Category|null
     */
    public function find(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }


    /**
     * @return array
     */
    public function getBestCategories(): array
    {
        $categories = $this->categoryRepository->findCategoriesHasOrder();

        $categoryIds = [];
        $categoryIndex = [];

        foreach ($categories as $category) {
            $categoryIds[] = $category->getId();
            $categoryIndex[$category->getId()] = $category;
        }

        $productsByCategory = $this->productService->getBestProductsByCategoryIds($categoryIds);

        foreach ($productsByCategory as $categoryId => $product) {
            if (isset($categoryIndex[$categoryId])) {
                $category = $categoryIndex[$categoryId];
                $category->setBestProduct($product);
            }
        }

        $this->preLoadAssociatedEntities($categories);
        return $categories;
    }


    /**
     * @return array
     */
    public
    function getCategories(): array
    {
        $categories = $this->categoryRepository->findAll();

        $categoriesWithChildren = [];
        foreach ($categories as $category) {
            if ($category->getParent() === null) {
                $categoriesWithChildren[] = $category;
            }
        }
        foreach ($categoriesWithChildren as $parentCategory) {
            foreach ($categories as $category) {
                if ($category->getParent() === $parentCategory) {
                    $parentCategory->addChild($category);
                }
            }
        }

        return $categoriesWithChildren;
    }

    /**
     * @return array
     */
    public
    function getChildrenCategories(): array
    {
        return $this->categoryRepository->findChildrenCategories();
    }

    /**
     * @param string $subCategorySlug
     * @param Category $category
     * @return Category|null
     */
    public function getSubCategoryBySlug(string $subCategorySlug, Category $category): ?Category
    {
        $subCategories = $category->getChildren();

        foreach ($subCategories as $subCategory) {
            if ($subCategory->getSlug() === $subCategorySlug) {
                return $subCategory;
            }
        }

        return null;
    }

    /**
     * @param array|null $sort
     * @param int|null $page
     * @param int|null $limit
     * @return array
     */
    public function getPaginatedCategories(?array $sort = null, ?int $page = null, ?int $limit = null): array
    {
        [$sort, $order] = $this->sortHelper->getOrderBy($sort);

        [$order, $page, $limit] = $this->sortHelper->setDefaultValues($order, $page, $limit);

        $queryBuilder = $this->categoryRepository->findAllQueryBuilder($sort, $order);

        return $this->paginationService->getPaginatedResult($queryBuilder, $page, $limit);

    }

    /**
     * @param Category $category
     * @return void
     */
    public function deleteCategory(Category $category): void
    {
        $this->categoryRepository->remove($category);
    }

    /**
     * @return CategoryFactory
     */
    public function getCategoryFactory(): CategoryFactory
    {
        return $this->categoryFactory;
    }

    /**
     * @param array $categories
     * @return void
     */
    private function preLoadAssociatedEntities(array $categories): void
    {
        foreach ($categories as $category) {
            $bestProduct = $category->getBestProduct();
            $products = $category->getProducts();

            foreach ($products as $product) {
                $category->addProduct($product);
            }

            if ($bestProduct) {
                $categories = $bestProduct->getCategories();
                $pictures = $bestProduct->getPictures();

                if ($categories instanceof PersistentCollection) {
                    $categories = new ArrayCollection($categories->getValues());
                }

                if ($pictures instanceof PersistentCollection) {
                    $pictures = new ArrayCollection($pictures->getValues());
                }

                $bestProduct->setCategories($categories);
                $bestProduct->setPictures($pictures);
            }
        }
    }
}