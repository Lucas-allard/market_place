<?php

namespace App\Form\UserForm;

use App\Entity\User;
use ReflectionClass;
use ReflectionException;
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
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
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
                    'input_sanitizer' => true,
                    'input_transformer' => true,
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
                    'input_sanitizer' => true,
                    'input_transformer' => true,
                ]);
        if (!$options['notBirthDate'] && $this->hasProperty('birthDate', $options))
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
                    'input_sanitizer' => true,
                ]);
        if (!$options['notPhone'])
            $builder
                ->add('phone', TextType::class, [
                    'label' => 'Téléphone',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir votre numéro de téléphone',
                        ]),
                    ],
                    'input_sanitizer' => true,
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
                    'input_sanitizer' => true,
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
                    'input_sanitizer' => true,
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
                    'input_sanitizer' => true,
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
                    'input_sanitizer' => true,
                    'input_transformer' => true,
                ]);
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
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


    /**
     * @param string $property
     * @param array $options
     * @return bool
     */
    public function hasProperty(string $property, array $options): bool
    {
        try {
            $entity = new ReflectionClass($options['data_class']);
        } catch (ReflectionException $e) {
            return false;
        }
        return $entity->hasProperty($property);
    }
}