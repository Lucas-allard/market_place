<?php

namespace App\Service\Brand;

use App\Repository\BrandRepository;

class BrandService
{
    /**
     * @var BrandRepository
     */
    private BrandRepository $brandRepository;

    /**
     * @param BrandRepository $brandRepository
     */
    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
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
        return $this->brandRepository->findTopBrands($limit);
    }
}