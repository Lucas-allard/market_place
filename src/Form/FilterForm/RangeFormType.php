<?php

namespace App\Form\FilterForm;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RangeFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('min', NumberType::class, [
                'required' => false,
                'attr' => [
                    'min' => $options['min'],
                    'max' => $options['max'],
                    'step' => 10,
                ],
                'label' => false,
            ])
            ->add('max', NumberType::class, [
                'required' => false,
                'attr' => [
                    'min' => $options['min'],
                    'max' => $options['max'],
                    'step' => 10,
                ],
                'label' => false,
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
            'min' => null,
            'max' => null,
        ]);
    }

}