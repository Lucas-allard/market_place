<?php

namespace App\Service\Form;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class FormProcessor
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    /**
     * @param string $type
     * @param object|null $data
     * @param array<string, mixed> $options
     * @return FormInterface
     */
    public function create(string $type, object $data = null, array $options = []): FormInterface
    {
        return $this->formFactory->create($type, $data, $options);
    }

    /**
     * @param Request $request
     * @param FormInterface $form
     * @return bool
     */
    public function process(Request $request, FormInterface $form): bool
    {
        $this->handleRequest($request, $form);

        if ($this->isValid($form)) {
            /** @var object $entity */
            $entity = $form->getData();
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            return true;
        }

        return false;
    }

    public function handleRequest(Request $request, FormInterface $form): void
    {
        $form->handleRequest($request);
    }

    public function isValid(FormInterface $form): bool
    {
        return $form->isSubmitted() && $form->isValid();
    }
}