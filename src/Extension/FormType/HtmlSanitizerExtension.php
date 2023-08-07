<?php

namespace App\Extension\FormType;

use App\DataTransformer\HtmlPurifierTransformer;
use App\DataTransformer\TrimTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeExtensionInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;


class HtmlSanitizerExtension implements FormTypeExtensionInterface
{
    /**
     * @var HtmlPurifierTransformer
     */
    private HtmlPurifierTransformer $htmlPurifierTransformer;
    /**
     * @var TrimTransformer
     */
    private TrimTransformer $trimTransformer;

    /**
     * @param HtmlPurifierTransformer $htmlPurifierTransformer
     * @param TrimTransformer $trimTransformer
     */
    public function __construct(HtmlPurifierTransformer $htmlPurifierTransformer, TrimTransformer $trimTransformer)
    {
        $this->htmlPurifierTransformer = $htmlPurifierTransformer;
        $this->trimTransformer = $trimTransformer;

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (isset($options['html_sanitizer']) && $options['html_sanitizer'] === true) {
            $builder->addModelTransformer($this->htmlPurifierTransformer)
                ->addModelTransformer($this->trimTransformer);
        }
    }

    /**
     * @return iterable
     */
    public static function getExtendedTypes(): iterable
    {
        return [TextareaType::class];
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
        $resolver->setDefined('html_sanitizer');
    }
}