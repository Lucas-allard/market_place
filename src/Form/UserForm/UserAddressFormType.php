<?php

namespace App\Form\UserForm;

use App\DataTransformer\StripTagTransformer;
use App\DataTransformer\TrimTransformer;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAddressFormType extends AbstractType
{
    private TrimTransformer $trimTransformer;
    private StripTagTransformer $stripTagTransformer;

    public function __construct(TrimTransformer $trimTransformer, StripTagTransformer $stripTagTransformer)
    {
        $this->trimTransformer = $trimTransformer;
        $this->stripTagTransformer = $stripTagTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Prénom',
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Nom',
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Ville',
                ],
            ])
            ->add('street', TextType::class, [
                'label' => 'Rue',
                'attr' => [
                    'placeholder' => 'Rue',
                ],
            ])
            ->add('streetNumber', TextType::class, [
                'label' => 'Numéro',
                'attr' => [
                    'placeholder' => 'Numéro',
                ],
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'attr' => [
                    'placeholder' => 'Code postal',
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => 'Téléphone',
                ],
            ])
        ;

        $this->addModelTransformers($builder);
    }

    private function addModelTransformers(FormBuilderInterface $builder): void
    {
        foreach ($builder->all() as $child) {
            $child->addModelTransformer($this->trimTransformer);
            $child->addModelTransformer($this->stripTagTransformer);
        }
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
