<?php

namespace App\Factory;

use App\Entity\Caracteristic;

class CaracteristicFactory
{
    /**
     * @return Caracteristic
     */
    public function create(): Caracteristic
    {
        return new Caracteristic();
    }
}