<?php

namespace App\Form\CategoryForm;

use App\Entity\Brand;
use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryFormType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la catégorie',
                'input_sanitizer' => true,
                'input_transformer' => true,
                'required' => true,

            ])
            ->add('description', CKEditorType::class, [
                'label' => 'Description de la catégorie',
                'html_sanitizer' => true,
                'required' => true,
            ])
            ->add('parent', EntityType::class, [
                'label' => 'Catégorie parente',
                'class' => Category::class,
                'attr' => [
                    'class' => 'tom-select'
                ],
                'query_builder' => function (EntityRepository $categoryRepository) {
                    return $categoryRepository->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC')
                        ->where('c.parent IS NULL');
                },
                'choice_label' => 'name',
                'multiple' => false,
                'required' => false,
            ])
            ->add('brands', EntityType::class, [
                'label' => 'Marque de la catégorie',
                'class' => Brand::class,
                'attr' => [
                    'class' => 'tom-select'
                ],
                'query_builder' => function (EntityRepository $brandRepository) {
                    return $brandRepository->createQueryBuilder('b')
                        ->orderBy('b.name', 'ASC');
                },
                'choice_label' => 'name',
                'expanded' => false,
                'multiple' => true,
                'by_reference' => false,
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
            'data_class' => Category::class,
        ]);
    }
}