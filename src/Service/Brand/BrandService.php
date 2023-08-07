<?php

namespace App\Service\Brand;

use App\Entity\Brand;
use App\Factory\BrandFactory;
use App\Repository\BrandRepository;
use App\Service\Pagination\PaginationService;
use App\Service\Utils\SortHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

class BrandService
{
    /**
     * @var BrandRepository
     */
    private BrandRepository $brandRepository;

    /**
     * @var SortHelper
     */
    private SortHelper $sortHelper;
    /**
     * @var PaginationService
     */
    private PaginationService $paginationService;
    /**
     * @var BrandFactory
     */
    private BrandFactory $brandFactory;

    /**
     * @param BrandRepository $brandRepository
     * @param SortHelper $sortHelper
     * @param PaginationService $paginationService
     * @param BrandFactory $brandFactory
     */
    public function __construct(
        BrandRepository $brandRepository,
        SortHelper $sortHelper,
        PaginationService $paginationService,
        BrandFactory $brandFactory
    )
    {
        $this->brandRepository = $brandRepository;
        $this->sortHelper = $sortHelper;
        $this->paginationService = $paginationService;
        $this->brandFactory = $brandFactory;
    }

    /**
     * @return array|null
     */
    public function getBrandsWithPictures(): ?array
    {
        return $this->brandRepository->findBrandsWithPictures();
    }

    /**
     * @param int $limit
     * @return array|null
     */
    public function getTopBrands(int $limit): ?array
    {

        $topBrands = $this->brandRepository->findTopBrands($limit);

        foreach ($topBrands as $brand) {
            $products = $brand->getProducts();

            foreach ($products as $product) {
                $categories = $product->getCategories();

                if ($categories instanceof PersistentCollection) {
                    $categories = new ArrayCollection($categories->getValues());
                }

                $product->setCategories($categories);
            }

            $brand->setProducts($products);
        }

        return $topBrands;
    }

    /**
     * @param array|null $sort
     * @param int|null $page
     * @param int|null $limit
     * @return array
     */
    public function getPaginatedBrands(?array $sort = null, ?int $page = null, ?int $limit = null): array
    {
        [$sort, $order] = $this->sortHelper->getOrderBy($sort);

        [$order, $page, $limit] = $this->sortHelper->setDefaultValues($order, $page, $limit);

        $query = $this->brandRepository->findAllQueryBuilder($sort, $order);

        return $this->paginationService->getPaginatedResult($query, $page, $limit);
    }

    /**
     * @return BrandFactory
     */
    public function getFactory(): BrandFactory
    {
        return $this->brandFactory;
    }

    /**
     * @param Brand $brand
     * @return void
     */
    public function deleteBrand(Brand $brand): void
    {
        $this->brandRepository->remove($brand, true);
    }
}