<?php

namespace App\Controller\Seller;

use App\Service\Order\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SELLER')]
#[Route('/ma-boutique', name: 'app_seller')]
class MainController extends AbstractController
{
    /**
     * @var OrderService
     */
    private OrderService $orderService;

    /**
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @return Response
     */
    #[Route('/', name: '_index')]
    public function index(): Response
    {
        $orders = $this->orderService->getOrdersForSeller($this->getUser());
        $incomesChart = $this->orderService->getTotalIncomesPerMonthChart($this->getUser(), $orders);

        $ordersChart = $this->orderService->getTotalOrdersPerMonthChart($orders);

        $productPerParentCategoryChart = $this->orderService->getTotalProductSoldPerCategoriesChart($this->getUser(), $orders, true);

        $productPerCategoryChart = $this->orderService->getTotalProductSoldPerCategoriesChart($this->getUser(), $orders, false);

        $topProductsChart = $this->orderService->getTotalTopProductsSoldChart($this->getUser(), $orders);

        $cartAverageChart = $this->orderService->getCartAveragePerMonthChart($this->getUser(), $orders);


        return $this->render('seller/main/index.html.twig', [
            'incomesChart' => $incomesChart,
            'ordersChart' => $ordersChart,
            'productPerParentCategoryChart' => $productPerParentCategoryChart,
            'productPerCategoryChart' => $productPerCategoryChart,
            'topProductsChart' => $topProductsChart,
            'cartAverageChart' => $cartAverageChart,
        ]);
    }
}
