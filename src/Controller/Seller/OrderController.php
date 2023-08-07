<?php

namespace App\Controller\Seller;

use App\Entity\Order;
use App\Entity\OrderItemSeller;
use App\Entity\Seller;
use App\Service\Order\OrderService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SELLER')]
#[Route('/ma-boutique/mes-ventes', name: 'app_seller_order')]
class OrderController extends AbstractController
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
     * @param Request $request
     * @return Response
     */
    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $parameters = $request->query->all();
        $sort = $parameters['sort'] ?? null;
        $page = $request->query->get('page');
        $limit = $request->query->get('limit');

        $orders = $this->orderService->getOrdersBySeller($this->getUser(), $sort, $page, $limit);

        return $this->render('seller/order/index.html.twig', [
            'orders' => $orders['data'],
            'pagination' => $orders['pagination'],
        ]);
    }

    /**
     * @param Order $order
     * @return Response
     */
    #[Route('/{slug}', name: '_show', methods: ['GET'])]
    public function show(Order $order): Response
    {
        /** @var Seller $seller */
        $seller = $this->getUser();

        try {
            $order = $this->orderService->getOrderForSeller($order, $seller);
        } catch (NonUniqueResultException $e) {
            $this->addFlash('danger', 'La commande n\'existe pas');

            return $this->redirectToRoute('app_seller_order_index');
        }


        return $this->render('seller/order/show.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @param Request $request
     * @param OrderItemSeller $orderItemSeller
     * @return Response
     */
    #[Route('/expedier/{id}', name: '_ship')]
    public function ship(Request $request, OrderItemSeller $orderItemSeller): Response
    {
        $token = $request->query->get('_csrf_token');

        $order = $orderItemSeller->getOrder();

        if (!$this->isCsrfTokenValid('order-ship-' . $order->getId(), $token)) {
            $this->addFlash('danger', 'Le token est invalide');

            return $this->redirectToRoute('app_seller_order_index');
        }

        /** @var Seller $seller */
        $seller = $this->getUser();

        $this->orderService->ship($order, $seller);

        $this->addFlash('success', 'La commande a été marqué comme expédiée');

        return $this->redirectToRoute('app_seller_order_index');
    }

    /**
     * @param Request $request
     * @param Order $order
     * @return JsonResponse
     */
    #[Route('/supprimer/{slug}', name: '_delete', methods: ['DELETE'])]
    public function delete(Request $request, Order $order): JsonResponse
    {
        $token = $request->headers->get('X-CSRF-Token');

        if (!$this->isCsrfTokenValid('order-delete-' . $order->getId(), $token)) {
            return $this->json([
                'status' => 'error',
                'message' => 'Le token est invalide',
            ], Response::HTTP_FORBIDDEN);
        }

        /** @var Seller $seller */
        $seller = $this->getUser();

        $this->orderService->deleteOrderForSeller($order, $seller);

        return $this->json([
            'status' => 'success',
            'message' => 'La commande a été supprimée',
        ], Response::HTTP_OK);
    }
}




