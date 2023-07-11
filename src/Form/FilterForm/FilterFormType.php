<?php

namespace App\Form\FilterForm;

use App\Entity\Brand;
use App\Entity\Caracteristic;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('price', RangeFormType::class, [
                'min' => $options['minPrice'],
                'max' => $options['maxPrice'],
            ])
            ->add('brand', EntityType::class, [
                'class' => Brand::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'query_builder' => static function (EntityRepository $er) use ($options) {
                    $category = $options['category'];
                    return $er->createQueryBuilder('b')
                        ->join('b.categories', 'c')
                        ->where('c.id = :category')
                        ->setParameter('category', $category->getId())
                        ->orderBy('b.name', 'ASC');
                },
            ])
            ->add('caracteristic', EntityType::class, [
                'class' => Caracteristic::class,
                'choice_label' => 'value',
                'multiple' => true,
                'expanded' => true,
                'group_by' => 'type',
                'query_builder' => static function (EntityRepository $er) use ($options) {
                    $category = $options['category'];
                    return $er->createQueryBuilder('c')
                        ->join('c.products', 'p')
                        ->join('p.categories', 'cat')
                        ->where('cat.id = :category')
                        ->setParameter('category', $category->getId())
                        ->groupBy('c.value')
                        ->orderBy('c.type', 'ASC')
                        ->addOrderBy('c.value', 'ASC');
                },
                'choice_attr' => function ($choice, $key, $value) {
                    /** @var Caracteristic $choice */
                    return ['data-type' => $choice->getType()];
                },
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
            'category' => null,
            'minPrice' => null,
            'maxPrice' => null,
            'method' => 'GET',
        ]);
    }
}
