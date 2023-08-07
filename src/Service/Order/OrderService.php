<?php

namespace App\Service\Order;

use App\Entity\Category;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderItemSeller;
use App\Entity\Seller;
use App\Repository\OrderRepository;
use App\Service\Chart\ChartService;
use App\Service\Pagination\PaginationService;
use App\Service\Utils\SortHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Model\Chart;

class OrderService
{
    /**
     * @var OrderRepository
     */
    private OrderRepository $orderRepository;

    /**
     * @var PaginationService
     */
    private PaginationService $paginationService;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @var ChartService $chartBuilder
     */
    private ChartService $chartService;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var SortHelper
     */
    private SortHelper $sortHelper;

    /**
     * @param OrderRepository $orderRepository
     * @param PaginationService $paginationService
     * @param TranslatorInterface $translator
     * @param ChartService $chartService
     * @param EntityManagerInterface $entityManager
     * @param SortHelper $sortHelper
     */
    public function __construct(
        OrderRepository        $orderRepository,
        PaginationService      $paginationService,
        TranslatorInterface    $translator,
        ChartService           $chartService,
        EntityManagerInterface $entityManager,
        SortHelper             $sortHelper
    )
    {
        $this->orderRepository = $orderRepository;
        $this->paginationService = $paginationService;
        $this->translator = $translator;
        $this->chartService = $chartService;
        $this->entityManager = $entityManager;
        $this->sortHelper = $sortHelper;
    }


    /**
     * @return array
     */
    public function getOrders(): array
    {
        return $this->orderRepository->findBy([], [
            'createdAt' => 'DESC',
        ]);
    }

    /**
     * @param int $id
     * @param string $status
     * @return Order|null
     */
    public function getOrder(int $id, string $status): ?Order
    {
        return $this->orderRepository->findOneBy([
            'id' => $id,
            'status' => $status,
        ]);
    }

    /**
     * @param UserInterface|null $customer
     * @param string $status
     * @return array
     */
    public function getOrdersByUser(?UserInterface $customer, string $status): array
    {
        return $this->orderRepository->findBy([
            'customer' => $customer,
            'status' => $status,
        ], [
            'updatedAt' => 'DESC',
        ]);
    }


    /**
     * @param UserInterface $user
     * @return array
     */
    public function getOrdersForSeller(UserInterface $user): array
    {
        return $this->orderRepository->findOrdersBySeller($user);
    }

    /**
     * @param Seller|null $user
     * @param array|null $sort
     * @param int|null $page
     * @param int|null $limit
     * @return array
     */
    public function getOrdersBySeller(
        ?Seller $user,
        ?array         $sort = null,
        ?int           $page = null,
        ?int           $limit = null
    ): array
    {
        [$sort, $order] = $this->sortHelper->getOrderBy($sort);

        [$order, $page, $limit] = $this->sortHelper->setDefaultValues($order, $page, $limit);

        $query = $this->orderRepository->findOrdersBySellerQuery($user, $sort, $order, $page, $limit);

        return $this->paginationService->getPaginatedResult($query, $page, $limit);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getOrderForSeller(Order $order, Seller $selller)
    {
        $order = $this->orderRepository->findOrderBySeller($order->getId(), $selller->getId());

        foreach ($order->getOrderItems() as $orderItem) {
            if ($orderItem->getProduct()->getSeller() !== $selller) {
                $order->removeOrderItem($orderItem);
            }
        }

        foreach ($order->getOrderItemSellers() as $orderItemSeller) {
            if ($orderItemSeller->getSeller() !== $selller) {
                $order->removeOrderItemSeller($orderItemSeller);
            }
        }

        return $order;
    }

    /**
     * @param Order $order
     * @return void
     */
    public function delete(Order $order): void
    {
        $this->orderRepository->remove($order, true);
    }

    /**
     * @param Order $order
     * @param Seller $seller
     * @return Order
     */
    public function ship(Order $order, Seller $seller): Order
    {
        $order->getOrderItemSeller($seller->getId())
            ->setStatus(OrderItemSeller::STATUS_ON_DELIVERY);

        $this->orderRepository->save($order, true);

        return $order;
    }

    /**
     * @param Order $order
     * @param Seller $seller
     * @return void
     */
    public function deleteOrderForSeller(Order $order, Seller $seller): void
    {
        $orderItemSeller = $this->getSeller($order, $seller);
        $orderItem = $this->getOrderItem($order, $seller);

        $order->removeOrderItem($orderItem);
        $order->removeOrderItemSeller($orderItemSeller);

        $this->orderRepository->save($order, true);
    }

    /**
     * @param Order $order
     * @param Seller $seller
     * @return OrderItemSeller|null
     */
    private function getSeller(Order $order, Seller $seller): ?OrderItemSeller
    {
        foreach ($order->getOrderItemSellers() as $orderItemSeller) {
            if ($orderItemSeller->getSeller() === $seller) {
                return $orderItemSeller;
            }
        }

        return null;
    }

    /**
     * @param Order $order
     * @param Seller $seller
     * @return OrderItem|null
     */
    private function getOrderItem(Order $order, Seller $seller): ?OrderItem
    {
        foreach ($order->getOrderItems() as $orderItem) {
            if ($orderItem->getProduct()->getSeller() === $seller) {
                return $orderItem;
            }
        }

        return null;
    }


    /**
     * @param UserInterface|null $user
     * @param array $orders
     * @return Chart
     */
    public function getTotalIncomesPerMonthChart(?UserInterface $user, array $orders): Chart
    {
        $total = [];

        $orderIds = array_map(fn($order) => $order->getId(), $orders);

        $results = $this->orderRepository->findTotalIncomesPerMonth($user, $orderIds);

        foreach ($results as $result) {
            $month = $this->translator->trans($result['month']);
            $totalIncome = $result['totalIncome'];
            $total[$month] = $totalIncome;
        }

        return $this->chartService->buildChart($total, Chart::TYPE_BAR);
    }

    /**
     * @param array $orders
     * @return Chart
     */
    public function getTotalOrdersPerMonthChart(array $orders): Chart
    {
        $total = [];

        foreach ($orders as $order) {
            $month = $this->translator->trans($order->getCreatedAt()->format('F'));
            if (!isset($total[$month])) {
                $total[$month] = 0;
            }

            $total[$month]++;
        }

        return $this->chartService->buildChart($total, Chart::TYPE_BAR, true);
    }


    /**
     * @param UserInterface|null $user
     * @param array $orders
     * @param bool $isParent
     * @return Chart
     */
    public function getTotalProductSoldPerCategoriesChart(?UserInterface $user, array $orders, bool $isParent): Chart
    {
        $totals = [];

        $orderIds = array_map(fn($order) => $order->getId(), $orders);

        $results = $this->orderRepository->findTotalProductSoldPerCategories($user, $orderIds, $isParent);
        foreach ($results as $result) {
            $categoryName = $result['categoryName'];
            $categoryId = $result['categoryId'];
            $totalQuantity = $result['totalQuantity'];

            if ($isParent) {
                $categoryKey = $categoryName;
            } else {
                $parentCategory = $this->entityManager->getRepository(Category::class)->find($categoryId)->getParent();
                if ($parentCategory) {
                    $categoryKey = $parentCategory->getName() . " > " . $categoryName;
                } else {
                    continue;
                }
            }

            ksort($totals);
            $totals[$categoryKey] = $totalQuantity;
        }

        return $this->chartService->buildChart($totals, Chart::TYPE_PIE, true);
    }


    /**
     * @param UserInterface|null $user
     * @param array $orders
     * @param int $limit
     * @return Chart
     */
    public function getTotalTopProductsSoldChart(?UserInterface $user, array $orders, int $limit = 10): Chart
    {

        $totals = [];

        $ordersIds = array_map(fn($order) => $order->getId(), $orders);

        $results = $this->orderRepository->findTotalTopProductsSold($user, $ordersIds);

        foreach ($results as $result) {
            $productName = $result['productName'];
            $totalQuantity = $result['totalQuantity'];

            $totals[$productName] = $totalQuantity;
        }

        return $this->chartService->buildChart($totals, Chart::TYPE_BAR, true);
    }


    /**
     * @param UserInterface $user
     * @param array $orders
     * @return Chart
     */
    public function getCartAveragePerMonthChart(UserInterface $user, array $orders): Chart
    {
        $totals = [];

        $orderIds = array_map(fn($order) => $order->getId(), $orders);

        $results = $this->orderRepository->findCartAveragePerMonth($user, $orderIds);

        foreach ($results as $result) {
            $month = $this->translator->trans($result['month']);
            $averageCart = round($result['averageCart'], 2);

            $totals[$month] = $averageCart;
        }

        return $this->chartService->buildChart($totals, Chart::TYPE_LINE);
    }
}