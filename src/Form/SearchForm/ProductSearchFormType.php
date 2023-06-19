<?php

namespace App\Form\SearchForm;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = $this->getChoices($options['categories']);


        $builder
            ->add('category', ChoiceType::class, [
                'required' => false,
                'placeholder' => 'Choisir une catégorie',
                'choices' => [
                        'Toutes les catégories' => null,
                    ] + $choices,
                'label' => null,
            ])
            ->add('product', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher un produit',
                ],
                'label' => null,

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'categories' => null,
            'formName' => null,
        ]);
    }

    private function getChoices(array $categories): array
    {
        $choices = [];

        foreach ($categories as $category) {
            if (!$choices[$category->getName()] = $category->getId()) {
                $choices[$category->getName()] = $category->getId();
            }
        }

        return $choices;
    }
}
