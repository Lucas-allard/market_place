<?php

namespace App\Extension\FormType;

use App\DataTransformer\HtmlPurifierTransformer;
use App\DataTransformer\TrimTransformer;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeExtensionInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;


class HtmlSanitizerExtension implements FormTypeExtensionInterface
{
    private HtmlPurifierTransformer $htmlPurifierTransformer;
    private TrimTransformer $trimTransformer;

    public function __construct(HtmlPurifierTransformer $htmlPurifierTransformer, TrimTransformer $trimTransformer)
    {
        $this->htmlPurifierTransformer = $htmlPurifierTransformer;
        $this->trimTransformer = $trimTransformer;

    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (isset($options['html_sanitizer']) && $options['html_sanitizer'] === true) {
            $builder->addModelTransformer($this->htmlPurifierTransformer)
                ->addModelTransformer($this->trimTransformer);
        }
    }

    /**
     * @inheritDoc
     */
    public static function getExtendedTypes(): iterable
    {
        return [TextareaType::class];
    }

    /**
     * @inheritDoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // TODO: Implement buildView() method.
    }

    /**
     * @inheritDoc
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        // TODO: Implement finishView() method.
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('html_sanitizer');
    }


}