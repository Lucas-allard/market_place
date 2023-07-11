<?php

namespace App\Form\RegistrationForm;

use App\Entity\Customer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerRegistrationType extends RegistrationFormType
{
    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}