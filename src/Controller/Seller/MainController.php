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
        $incomesChart = $this->orderService->getTotalIncomesPerMonthChart($this->getUser());

        $ordersChart = $this->orderService->getTotalOrdersPerMonthChart($this->getUser());

        $productPerParentCategoryChart = $this->orderService->getTotalProductSoldPerCategoriesChart($this->getUser(), true);

        $productPerCategoryChart = $this->orderService->getTotalProductSoldPerCategoriesChart($this->getUser(), false);

        $topProductsChart = $this->orderService->getTotalTopProductsSoldChart($this->getUser());

        $cartAverageChart = $this->orderService->getCartAveragePerMonthChart($this->getUser());


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
