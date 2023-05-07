<?php

namespace App\Form\Registration;

use App\Entity\Customer;
use App\Entity\Seller;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SellerRegistrationType extends RegistrationFormType
{

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Seller::class,
        ]);
    }
}