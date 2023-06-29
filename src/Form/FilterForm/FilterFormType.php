<?php

namespace App\Form\FilterForm;

use App\DataTransformer\StripTagTransformer;
use App\DataTransformer\TrimTransformer;
use App\Entity\Brand;
use App\Entity\Caracteristic;
use Doctrine\ORM\EntityRepository;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterFormType extends AbstractType
{
    private StripTagTransformer $stripTagTransformer;
    private TrimTransformer $trimTransformer;
    public function __construct(StripTagTransformer $stripTagTransformer, TrimTransformer $trimTransformer)
    {
        $this->stripTagTransformer = $stripTagTransformer;
        $this->trimTransformer = $trimTransformer;
    }

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

//        $this->addModelTransformer($builder);
    }

    private function addModelTransformer(FormBuilderInterface $builder): void
    {
        foreach ($builder->all() as $child) {
            $child->addModelTransformer($this->stripTagTransformer);
            $child->addModelTransformer($this->trimTransformer);
        }
    }

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
