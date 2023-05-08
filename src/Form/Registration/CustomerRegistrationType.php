<?php

namespace App\Form\Registration;

use App\Entity\Customer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerRegistrationType extends RegistrationFormType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}