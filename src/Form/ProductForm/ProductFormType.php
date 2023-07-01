<?php

namespace App\Form\ProductForm;

use App\Entity\Brand;
use App\Entity\Caracteristic;
use App\Entity\Category;
use App\Entity\Product;
use App\EventListener\AddPictureFieldListener;
use App\Form\PictureForm\PictureFormType;
use Doctrine\ORM\EntityRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ProductFormType extends AbstractType
{
    private AddPictureFieldListener $addPictureFieldListener;

    public function __construct(AddPictureFieldListener $addPictureFieldListener)
    {
        $this->addPictureFieldListener = $addPictureFieldListener;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'input_sanitizer' => true,
                'input_transformer' => true,

            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix du produit',
                'currency' => 'EUR',
                'grouping' => true,
            ])
            ->add('discount', PercentType::class, [
                'label' => 'Réduction du produit',
                'type' => 'integer',
                'scale' => 2,
                'attr' => [
                    'min' => 0,
                    'max' => 1,
                    'step' => 0.01,
                ],
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité en stock',
                'attr' => [
                    'min' => 0,
                ],
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'Description du produit',
                'html_sanitizer' => true,
            ])
            ->add('categories', EntityType::class, [
                'label' => 'Catégories du produit',
                'class' => Category::class,
                'attr' => [
                    'class' => 'tom-select'
                ],
                'query_builder' => function (EntityRepository $categoryRepository) {
                    return $categoryRepository->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC')
                        ->where('c.parent IS NOT NULL');
                },
                'choice_label' => function (Category $category) {
                    return sprintf('%s > %s', $category->getParent()->getName(), $category->getName());
                },
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('brand', EntityType::class, [
                'label' => 'Marque du produit',
                'class' => Brand::class,
                'attr' => [
                    'class' => 'tom-select'
                ],
                'query_builder' => function (EntityRepository $brandRepository) {
                    return $brandRepository->createQueryBuilder('b')
                        ->orderBy('b.name', 'ASC');
                },
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('caracteristics', EntityType::class, [
                'label' => 'Caractéristiques du produit',
                'class' => Caracteristic::class,
                'attr' => [
                    'class' => 'tom-select'
                ],
                'choice_label' => function (Caracteristic $caracteristic) {
                    return sprintf('%s > %s', $caracteristic->getType(), $caracteristic->getValue());
                },
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('pictures', CollectionType::class, [
                'label' => false,
                'entry_type' => PictureFormType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
                'mapped' => false,
            ])
            ->addEventSubscriber($this->addPictureFieldListener)
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}