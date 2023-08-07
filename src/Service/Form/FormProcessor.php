<?php

namespace App\Service\Form;

use Doctrine\ORM\EntityManagerInterface;
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
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @return FormProcessor
     */
    public function setEntityManager(EntityManagerInterface $entityManager): FormProcessor
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * @return FormFactoryInterface
     */
    public function getFormFactory(): FormFactoryInterface
    {
        return $this->formFactory;
    }

    /**
     * @param FormFactoryInterface $formFactory
     * @return FormProcessor
     */
    public function setFormFactory(FormFactoryInterface $formFactory): FormProcessor
    {
        $this->formFactory = $formFactory;
        return $this;
    }

    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;

    /**
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface $formFactory
     */
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
            $this->save($form);
            return true;
        }

        return false;
    }

    /**
     * @param Request $request
     * @param FormInterface $form
     * @return void
     */
    public function handleRequest(Request $request, FormInterface $form): void
    {
        $form->handleRequest($request);
    }

    /**
     * @param FormInterface $form
     * @return bool
     */
    public function isValid(FormInterface $form): bool
    {
        return $form->isSubmitted() && $form->isValid();
    }

    /**
     * @param object $data
     * @return void
     */
    public function save(object $data): void
    {
        $this->entityManager->persist($data->getData());
        $this->entityManager->flush();
    }
}