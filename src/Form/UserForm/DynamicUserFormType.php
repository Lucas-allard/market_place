<?php

namespace App\Form\UserForm;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class DynamicUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['notFirstname'])
            $builder
                ->add('firstname', TextType::class, [
                    'label' => 'Prénom',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir votre prénom',
                        ]),
                        new Length([
                            'min' => 2,
                            'minMessage' => 'Votre prénom doit contenir au moins {{ limit }} caractères',
                            'max' => 50,
                            'maxMessage' => 'Votre prénom doit contenir au maximum {{ limit }} caractères',
                        ])
                    ],
                ]);
        if (!$options['notLastname'])
            $builder
                ->add('lastname', TextType::class, [
                    'label' => 'Nom',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir votre nom',
                        ]),
                        new Length([
                            'min' => 2,
                            'minMessage' => 'Votre nom doit contenir au moins {{ limit }} caractères',
                            'max' => 50,
                            'maxMessage' => 'Votre nom doit contenir au maximum {{ limit }} caractères',
                        ])
                    ],
                ]);
        if (!$options['notBirthDate'])
            $builder
                ->add('birthDate', DateType::class, [
                    'label' => 'Date de naissance',
                    'widget' => 'single_text',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir votre date de naissance',
                        ]),
                    ],
                ]);
        if (!$options['notEmail'])
            $builder
                ->add('email', EmailType::class, [
                    'label' => 'Email',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir votre email',
                        ]),
                    ],
                ]);
        if (!$options['notPhone'])
            $builder
                ->add('phone', TelType::class, [
                    'label' => 'Téléphone',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir votre numéro de téléphone',
                        ]),
                    ],
                ]);
        if (!$options['notPostalCode'])
            $builder
                ->add('postalCode', TextType::class, [
                    'label' => 'Code postal',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir votre code postal',
                        ]),
                    ],
                ]);
        if (!$options['notStreet'])
            $builder
                ->add('street', TextType::class, [
                    'label' => 'Rue',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir votre rue',
                        ]),
                    ],
                ]);
        if (!$options['notStreetNumber'])
            $builder
                ->add('streetNumber', TextType::class, [
                    'label' => 'Numéro',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir votre numéro',
                        ]),
                    ],
                ]);
        if (!$options['notCity'])
            $builder
                ->add('city', TextType::class, [
                    'label' => 'Ville',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir votre ville',
                        ]),
                    ],
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'notFirstname' => false,
            'notLastname' => false,
            'notBirthDate' => false,
            'notEmail' => false,
            'notPhone' => false,
            'notPostalCode' => false,
            'notCity' => false,
            'notStreetNumber' => false,
            'notStreet' => false,
        ]);
    }

}