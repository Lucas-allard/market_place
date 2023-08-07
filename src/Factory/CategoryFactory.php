<?php

namespace App\Factory;

use App\Entity\Category;


class CategoryFactory
{
    /**
     *
     * @return Category
     */
    public function create(): Category
    {
        return new Category();
    }
}