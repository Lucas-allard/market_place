<?php

namespace App\Extension\FormType;

use App\DataTransformer\StripTagTransformer;
use App\DataTransformer\TrimTransformer;
use App\DataTransformer\UcFirstTransformer;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeExtensionInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InputSanitizerExtension implements FormTypeExtensionInterface
{
    private StripTagTransformer $stripTagTransformer;
    private TrimTransformer $trimTransformer;
    private UcFirstTransformer $ucFirstTransformer;

    public function __construct(StripTagTransformer $stripTagTransformer, TrimTransformer $trimTransformer, UcFirstTransformer $ucFirstTransformer)
    {
        $this->stripTagTransformer = $stripTagTransformer;
        $this->trimTransformer = $trimTransformer;
        $this->ucFirstTransformer = $ucFirstTransformer;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (isset($options['input_sanitizer']) && $options['input_sanitizer'] === true) {
            $builder->addModelTransformer($this->stripTagTransformer)
                ->addModelTransformer($this->trimTransformer);
        }
    }

    public static function getExtendedTypes(): iterable
    {
        return [TextType::class];
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
        $resolver->setDefined('input_sanitizer');
    }
}