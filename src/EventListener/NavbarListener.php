<?php

namespace App\EventListener;

use App\Event\NavbarEvent;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Twig\Environment;


class NavbarListener
{
    private Environment $twig;
    private CategoryRepository $categoryRepository;

    public function __construct(
        Environment  $twig,
        CategoryRepository $categoryRepository
    )
    {
        $this->twig = $twig;
        $this->categoryRepository = $categoryRepository;
    }

    public function onKernelRequest(): void
    {
        $categories = $this->categoryRepository->getParentsAndChildrenCategoriesInSeparatedArrays();
        $this->twig->addGlobal('categories', $categories);
    }
}