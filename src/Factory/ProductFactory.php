<?php

namespace App\Factory;

use App\Entity\Product;
use Symfony\Bundle\SecurityBundle\Security;

class ProductFactory
{

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @return Product
     */
    public function create(): Product
    {
        return (new Product())->setSeller($this->security->getUser());
    }
}