<?php

namespace App\Extension\FormType;

use App\DataTransformer\UcFirstTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeExtensionInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InputTransformExtension implements FormTypeExtensionInterface
{
    /**
     * @var UcFirstTransformer
     */
    private UcFirstTransformer $ucFirstTransformer;

    /**
     * @param UcFirstTransformer $ucFirstTransformer
     */
    public function __construct(UcFirstTransformer $ucFirstTransformer)
    {

        $this->ucFirstTransformer = $ucFirstTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (isset($options['input_transformer']) && $options['input_transformer'] === true) {
            $builder->addModelTransformer($this->ucFirstTransformer);
        }
    }

    /**
     * @return iterable
     */
    public static function getExtendedTypes(): iterable
    {
        return [TextType::class];
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     * @return void
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // TODO: Implement buildView() method.
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     * @return void
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        // TODO: Implement finishView() method.
    }

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('input_transformer');
    }
}