<?php

namespace App\Form\RegistrationForm;

use App\Entity\Seller;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SellerRegistrationType extends RegistrationFormType
{

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Seller::class,
        ]);
    }
}