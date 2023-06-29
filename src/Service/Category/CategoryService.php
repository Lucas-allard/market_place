<?php

namespace App\Service\Category;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Service\Product\ProductService;

class CategoryService
{
    private CategoryRepository $categoryRepository;

    private ProductService $productService;

    public function __construct(CategoryRepository $categoryRepository, ProductService $productService)
    {
        $this->categoryRepository = $categoryRepository;
        $this->productService = $productService;
    }

    public function find(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }


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


    public
    function getParentsAndChildrenCategoriesInSeparatedArrays(): array
    {
        return $this->categoryRepository->findParentsAndChildrenCategoriesInSeparatedArrays();
    }

    public
    function getChildrenCategories(): array
    {
        return $this->categoryRepository->findChildrenCategories();
    }

}