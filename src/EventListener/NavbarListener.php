<?php

namespace App\EventListener;

use App\Form\SearchForm\ProductSearchFormType;
use App\Service\Category\CategoryService;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Environment;


class NavbarListener
{
    private Environment $twig;
    private CategoryService $categoryService;
    private FormFactoryInterface $form;

    public function __construct(
        Environment  $twig,
        CategoryService $categoryService,
        FormFactoryInterface $form
    )
    {
        $this->twig = $twig;
        $this->categoryService = $categoryService;
        $this->form = $form;
    }

    public function onKernelRequest(): void
    {
        $categories = $this->categoryService->getParentsAndChildrenCategoriesInSeparatedArrays();

        $childrenCategories = $this->categoryService->getChildrenCategories();

        $searchForm = $this->form->create(ProductSearchFormType::class, null, [
            'categories' => $childrenCategories,
            'method' => 'GET',
            'formName' => 'product_search_form',
        ]);

        $searchFormView = $searchForm->createView();

        $this->twig->addGlobal('categories', $categories);
        $this->twig->addGlobal('searchFormView', $searchFormView);
    }
}