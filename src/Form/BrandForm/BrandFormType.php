<?php

namespace App\Form\BrandForm;

use App\Entity\Brand;
use App\EventListener\AddPictureFieldListener;
use App\Form\PictureForm\PictureFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BrandFormType extends AbstractType
{

    /**
     * @var AddPictureFieldListener
     */
    private AddPictureFieldListener $addPictureFieldListener;

    /**
     * @param AddPictureFieldListener $addPictureFieldListener
     */
    public function __construct(AddPictureFieldListener $addPictureFieldListener)
    {
        $this->addPictureFieldListener = $addPictureFieldListener;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la marque',
                'input_sanitizer' => true,
                'input_transformer' => true,
                'required' => true,
            ])
            ->add('picture', PictureFormType::class, [
                'label' => 'Logo de la marque',
                'required' => false,
                'thumbnail' => $options['thumbnail'],
            ])
            ->addEventSubscriber($this->addPictureFieldListener)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Brand::class,
            'thumbnail' => false,
        ]);
    }
}