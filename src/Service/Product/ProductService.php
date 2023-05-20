<?php

namespace App\Service\Product;

use App\Repository\ProductRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

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
}