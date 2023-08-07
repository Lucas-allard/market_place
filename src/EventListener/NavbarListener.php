<?php

namespace App\EventListener;

use App\Form\SearchForm\ProductSearchFormType;
use App\Service\Category\CategoryService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Twig\Environment;

class NavbarListener
{
    /**
     * @var Environment
     */
    private Environment $twig;
    /**
     * @var CategoryService
     */
    private CategoryService $categoryService;
    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $form;

    /**
     * @var CacheInterface
     */
    private CacheInterface $cache;

    /**
     * @param Environment $twig
     * @param CategoryService $categoryService
     * @param FormFactoryInterface $form
     * @param CacheInterface $cache
     */
    public function __construct(
        Environment  $twig,
        CategoryService $categoryService,
        FormFactoryInterface $form,
        CacheInterface $cache
    )
    {
        $this->twig = $twig;
        $this->categoryService = $categoryService;
        $this->form = $form;
        $this->cache = $cache;
    }

    /**
     * @return void
     */
    public function onKernelRequest(): void
    {
        $categories = $this->cache->get('categories', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            return $this->categoryService->getCategories();
        });

        $childrenCategories = $this->cache->get('children_categories', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            return $this->categoryService->getChildrenCategories();
        });


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