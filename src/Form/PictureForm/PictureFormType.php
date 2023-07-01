<?php

namespace App\Form\PictureForm;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PictureFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('path', FileType::class, [
                'label' => 'Image',
                'attr' => [
                    'placeholder' => 'Image',
                    'accept' => 'image/jpeg, image/jpg, image/png',
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez choisir un fichier valide (jpg, jpeg, png)',
                    ]),
                ],
            ])
            ->add('alt', TextType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Description',
                ],
                'input_sanitizer' => true,
                'input_transformer' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
