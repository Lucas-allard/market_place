<?php

namespace App\Service\Category;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Service\Product\ProductService;

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
     * @param CategoryRepository $categoryRepository
     * @param ProductService $productService
     */
    public function __construct(CategoryRepository $categoryRepository, ProductService $productService)
    {
        $this->categoryRepository = $categoryRepository;
        $this->productService = $productService;
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

        $products = $this->productService->getBestProductsByCategoryIds($categoryIds);

        foreach ($products as $product) {
            $productCategories = $product->getCategories();

            foreach ($productCategories as $productCategory) {
                $categoryId = $productCategory->getId();

                if (isset($categoryIndex[$categoryId])) {
                    $category = $categoryIndex[$categoryId];
                    $category->setBestProduct($product);
                    unset($categoryIndex[$categoryId]);
                    break;
                }
            }

            if (empty($categoryIndex)) {
                break;
            }
        }

        return $categories;
    }


    /**
     * @return array
     */
    public
    function getParentsAndChildrenCategoriesInSeparatedArrays(): array
    {
        return $this->categoryRepository->findParentsAndChildrenCategoriesInSeparatedArrays();
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
    public function getSubCategoryBySlug(string $subCategorySlug, Category $category)
    {
        $subCategories = $category->getChildren();

        foreach ($subCategories as $subCategory) {
            if ($subCategory->getSlug() === $subCategorySlug) {
                return $subCategory;
            }
        }

        return null;
    }

}