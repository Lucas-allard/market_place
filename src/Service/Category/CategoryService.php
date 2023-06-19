<?php

namespace App\Service\Category;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\DBAL\Exception;

class CategoryService
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function find(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }

    /**
     * @throws Exception
     */
    public function getCategoriesHavingMostProductsAndBestProduct(): array
    {
        return $this->categoryRepository->findCategoriesHavingMostProductsAndBestProduct();
    }

    public function getParentsAndChildrenCategoriesInSeparatedArrays(): array
    {
        return $this->categoryRepository->findParentsAndChildrenCategoriesInSeparatedArrays();
    }

    public function getChildrenCategories(): array
    {
        return $this->categoryRepository->findChildrenCategories();
    }

}