<?php

namespace App\Form\RegistrationForm;

use App\Entity\Customer;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckoutRegistrationFormType extends RegistrationFormType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Ville',
                ],
                'required' => true,
                'input_sanitizer' => true,
                'input_transformer' => true,
            ])
            ->add('street', TextType::class, [
                'label' => 'Rue',
                'attr' => [
                    'placeholder' => 'Rue',
                ],
                'required' => true,
                'input_sanitizer' => true,
            ])
            ->add('streetNumber', TextType::class, [
                'label' => 'Numéro',
                'attr' => [
                    'placeholder' => 'Numéro',
                ],
                'required' => true,
                'input_sanitizer' => true,
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'attr' => [
                    'placeholder' => 'Code postal',
                ],
                'required' => true,
                'input_sanitizer' => true,
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => 'Téléphone',
                ],
                'required' => true,
                'input_sanitizer' => true,
            ])
        ;

    }

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
