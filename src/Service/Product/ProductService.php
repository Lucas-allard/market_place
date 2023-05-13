<?php

namespace App\Service\Product;

use App\Repository\ProductRepository;

class ProductService
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
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

    public function getTopProductsWithCategories(int $maxResults): array
    {
        return $this->productRepository->findTopProductsWithCategories($maxResults);
    }
}