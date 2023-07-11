<?php

namespace App\Service\Order;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\OrderItemSeller;
use App\Entity\Seller;
use App\Repository\OrderRepository;
use App\Service\Chart\ChartService;
use App\Service\Pagination\PaginationService;
use App\Service\SortableInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\UX\Chartjs\Model\Chart;

class OrderService implements SortableInterface
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
     * @var ChartService $chartBuilder
     */
    private ChartService $chartService;

    /**
     * @param OrderRepository $orderRepository
     * @param PaginationService $paginationService
     * @param ChartService $chartService
     */
    public function __construct(
        OrderRepository       $orderRepository,
        PaginationService     $paginationService,
        ChartService $chartService
    )
    {
        $this->orderRepository = $orderRepository;
        $this->paginationService = $paginationService;
        $this->chartService = $chartService;
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
     * @param UserInterface|null $user
     * @param array|null $sort
     * @param int|null $page
     * @param int|null $limit
     * @return array
     */
    public function getOrdersBySeller(
        ?UserInterface $user,
        ?array         $sort = null,
        ?int           $page = null,
        ?int           $limit = null
    ): array
    {
        [$sort, $order] = $this->getOrderBy($sort);

        [$order, $page, $limit] = $this->setDefaultValues($order, $page, $limit);

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
     * @return Chart
     */
    public function getTotalIncomesPerMonthChart(?UserInterface $user): Chart
    {
        $orders = $this->orderRepository->findOrdersBySeller($user);

        $total = [];

        foreach ($orders as $order) {
            foreach ($order->getOrderItems() as $orderItem) {
                if ($orderItem->getProduct()->getSeller() === $user) {
                    $month = $order->getCreatedAt()->format('Y-m');
                    if (!isset($total[$month])) {
                        $total[$order->getCreatedAt()->format('Y-m')] = 0;
                    }

                    $total[$month] += $orderItem->getTotal();
                }
            }
        }

        return $this->chartService->buildChart($total, Chart::TYPE_BAR,);
    }

    /**
     * @param UserInterface|null $user
     * @return Chart
     */
    public function getTotalOrdersPerMonthChart(?UserInterface $user): Chart
    {
        $orders = $this->orderRepository->findOrdersBySeller($user);

        $total = [];

        foreach ($orders as $order) {
            $month = $order->getCreatedAt()->format('Y-m');
            if (!isset($total[$month])) {
                $total[$order->getCreatedAt()->format('Y-m')] = 0;
            }

            $total[$month] += 1;
        }

        return $this->chartService->buildChart($total, Chart::TYPE_BAR);
    }


    /**
     * @param UserInterface|null $user
     * @param bool $isParent
     * @return Chart
     */
    public function getTotalProductSoldPerCategoriesChart(?UserInterface $user, bool $isParent): Chart
    {
        $orders = $this->orderRepository->findOrdersBySeller($user);

        $totals = [];

        foreach ($orders as $order) {
            foreach ($order->getOrderItems() as $orderItem) {
                if ($orderItem->getProduct()->getSeller() === $user) {
                    foreach ($orderItem->getProduct()->getCategories() as $category) {
                        if ($isParent && $category->getParent() === null) {
                            $categoryKey = $category->getName();
                        } elseif (!$isParent && $category->getParent() !== null) {
                            $categoryKey = $category->getParent()->getName() . " > " . $category->getName();
                        } else {
                            continue;
                        }

                        if (!isset($totals[$categoryKey])) {
                            $totals[$categoryKey] = 0;
                        }

                        $totals[$categoryKey] += $orderItem->getQuantity();
                    }
                }
            }
        }

        arsort($totals);

        return $this->chartService->buildChart($totals, Chart::TYPE_PIE, true);
    }


    /**
     * @param UserInterface|null $user
     * @param int $limit
     * @return Chart
     */
    public function getTotalTopProductsSoldChart(?UserInterface $user, int $limit = 10): Chart
    {
        $orders = $this->orderRepository->findOrdersBySeller($user);

        $totals = [];

        foreach ($orders as $order) {
            foreach ($order->getOrderItems() as $orderItem) {
                if ($orderItem->getProduct()->getSeller() === $user) {
                    $productName = $orderItem->getProduct()->getName();
                    $quantity = $orderItem->getQuantity();

                    if (!isset($totals[$productName])) {
                        $totals[$productName] = 0;
                    }

                    $totals[$productName] += $quantity;
                }
            }
        }

        arsort($totals);

        $totals = array_slice($totals, 0, $limit, true);

        return $this->chartService->buildChart($totals, Chart::TYPE_BAR, true);
    }


    public function getCartAveragePerMonthChart(?UserInterface $getUser): Chart
    {
        $orders = $this->orderRepository->findOrdersBySeller($getUser);

        $totals = [];
        $weeks = [];

        foreach ($orders as $order) {
            $weekNumber = $order->getOrderDate()->format('m');

            if (!isset($totals[$weekNumber])) {
                $totals[$weekNumber] = 0;
                $weeks[$weekNumber] = 0;
            }

            foreach ($order->getOrderItems() as $orderItem) {
                if ($orderItem->getProduct()->getSeller() === $getUser) {
                    $totals[$weekNumber] += $orderItem->getTotal();
                    $weeks[$weekNumber]++;
                }
            }

        }

        $averages = [];

        foreach ($totals as $weekNumber => $total) {
            $average = $total / $weeks[$weekNumber];
            $averages[$weekNumber] = $average;
        }

        ksort($averages);

        return $this->chartService->buildChart($averages, Chart::TYPE_LINE);
    }


    /**
     * @param array|null $criteria
     * @return array
     */
    public function getOrderBy(?array $criteria): array
    {
        if ($criteria) {
            $sort = array_key_first($criteria);
            $order = $criteria[$sort];

            return [$sort, $order];
        }

        return ['createdAt', 'DESC'];
    }

    /**
     * @param string|null $order
     * @param int|null $page
     * @param int|null $limit
     * @return array
     */
    public function setDefaultValues(?string &$order, ?int &$page, ?int &$limit): array
    {
        $order = $order ?? 'createdAt';
        $page = $page ?? 1;
        $limit = $limit ?? 16;

        return [$order, $page, $limit];
    }
}