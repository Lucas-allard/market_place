<?php

namespace App\Controller\Admin;

use App\Service\Order\OrderService;
use App\Service\Product\ProductService;
use App\Service\User\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Cache\CacheInterface;

#[Route('/admin', name: 'app_admin')]
#[IsGranted('ROLE_ADMIN')]
class MainController extends AbstractController
{
    /**
     * @var UserService
     */
    private UserService $userService;

    /**
     * @var ProductService
     */
    private ProductService $productService;

    /**
     * @var OrderService
     */
    private OrderService $orderService;

    /**
     * @param UserService $userService
     * @param ProductService $productService
     * @param OrderService $orderService
     */
    public function __construct(UserService $userService, ProductService $productService, OrderService $orderService)
    {
        $this->userService = $userService;
        $this->productService = $productService;
        $this->orderService = $orderService;
    }

    /**
     * @param CacheInterface $cache
     * @return Response
     */
    #[Route('/dashboard', name: '_dashboard')]
    public function index(CacheInterface $cache): Response
    {
        $users = $this->userService->getUsers();
        $registrationsPerMonth = $this->userService->getRegistrationsPerMonthChart($users);
        $sellerRegistrationsPerMonth = $this->userService->getRegistrationByRolePerMonthChart($users, 'ROLE_SELLER');
        $customerRegistrationsPerMonth = $this->userService->getRegistrationByRolePerMonthChart($users, 'ROLE_CUSTOMER');

        $products = $this->productService->getProducts();
        $productsPerMonth = $this->productService->getProductsPerMonthChart($products);
        $productsPerCategory = $this->productService->getProductsPerCategoryChart($products);
        $productsPerSeller = $this->productService->getProductsPerSellerChart($products);

        $orders = $this->orderService->getOrders();
        $ordersPerMonth = $this->orderService->getTotalOrdersPerMonthChart($orders);

        return $this->render('admin/dashboard/index.html.twig', [
            'registrationsPerMonth' => $registrationsPerMonth,
            'sellerRegistrationsPerMonth' => $sellerRegistrationsPerMonth,
            'customerRegistrationsPerMonth' => $customerRegistrationsPerMonth,
            'productsPerMonth' => $productsPerMonth,
            'productsPerCategory' => $productsPerCategory,
            'productsPerSeller' => $productsPerSeller,
            'ordersPerMonth' => $ordersPerMonth,
      ]);
    }
}
