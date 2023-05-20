<?php

namespace App\Service\Brand;

use App\Entity\Brand;
use App\Repository\BrandRepository;

class BrandService
{
    private BrandRepository $brandRepository;

    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function getBrandsWithPictures(): ?array
    {
        return $this->brandRepository->findBrandsWithPictures();
    }

    public function getTopBrands(int $limit): ?array
    {
        return $this->brandRepository->findTopBrands($limit);
    }
}