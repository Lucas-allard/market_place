<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\UserForm\DynamicUserFormType;
use App\Service\Form\FormProcessor;
use App\Service\Order\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/mon-compte', name: 'app_user')]
class UserController extends AbstractController
{
    /**
     * @var FormProcessor
     */
    private FormProcessor $formProcessor;

    /**
     * @param FormProcessor $formProcessor
     */
    public function __construct(FormProcessor $formProcessor)
    {
        $this->formProcessor = $formProcessor;
    }

    /**
     * @param OrderService $orderService
     * @return Response
     */
    #[Route('/mes-commandes-en-cours', name: '_order')]
    public function index(OrderService $orderService): Response
    {
        $orders = $orderService->getOrdersByUser($this->getUser(), Order::STATUS_PENDING);

        return $this->render('customer/order/orders.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @param OrderService $orderService
     * @return Response
     */
    #[Route('/mes-commandes-terminees', name: '_order_history')]
    public function orderHistory(OrderService $orderService): Response
    {
        $orders = $orderService->getOrdersByUser($this->getUser(), Order::STATUS_COMPLETED);

        return $this->render('customer/order/orders.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/mes-informations', name: '_info')]
    public function info(Request $request): Response
    {
        $form = $this->formProcessor->create(DynamicUserFormType::class, $this->getUser());

        if ($this->formProcessor->process($request, $form)) {
            $this->addFlash('success', 'Vos informations ont bien été mises à jour');
            return $this->redirectToRoute('app_user_info');
        }

        return $this->render('user/info/info.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
