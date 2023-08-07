<?php

namespace App\Factory;

use App\Entity\Brand;

class BrandFactory
{
    /**
     * @return Brand
     */
    public function create(): Brand
    {
        return new Brand();
    }
}