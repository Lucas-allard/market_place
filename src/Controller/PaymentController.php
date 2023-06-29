<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Payment;
use App\Manager\CartManager;
use App\Manager\PaymentManager;
use App\Service\Order\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_USER')]
#[Route('/paiement', name: 'app_payment')]
class PaymentController extends AbstractController
{
    private CartManager $cartManager;
    private PaymentManager $paymentManager;

    public function __construct(
        CartManager    $cartManager,
        PaymentManager $paymentManager,
    )
    {
        $this->cartManager = $cartManager;
        $this->paymentManager = $paymentManager;
    }

    #[Route('/', name: '_index')]
    public function index(): Response
    {
        $cart = $this->cartManager->getCurrentCart();

        $payment = $this->paymentManager->getPayment($cart);

        $this->paymentManager->savePayment($payment);

        $paymentSession = $this->paymentManager->getPaymentSession($payment);

        return new RedirectResponse($paymentSession->url, 302, ['Content-Type' => 'application/json']);
    }

    #[Route('/succes/{id}', name: '_success')]
    public function paymentSuccess(Order $order): Response
    {
        if ($order->getOrderStatus() === Order::STATUS_CART) {
            $order->setOrderStatus(Order::STATUS_PENDING);
            $this->cartManager->saveCart($order);
        }

        $payment = $this->paymentManager->getPayment($order);

        if ($order->getOrderStatus() === Order::STATUS_PENDING && $payment->getStatus() === Payment::STATUS_PENDING) {
            $payment->setStatus(Payment::STATUS_PAID);
            $this->paymentManager->savePayment($payment);
        }

        $this->addFlash('success', 'Votre commande a bien été validée, vous allez recevoir un mail de confirmation !');

        return $this->render('payment/index.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/echec/{id}', name: '_failed')]
    public function paymentFailed(Order $order): Response
    {
        $payment = $this->paymentManager->getPayment($order);

        if ($payment->getStatus() === Payment::STATUS_PENDING) {
            $payment->setStatus(Payment::STATUS_FAILED);
            $this->paymentManager->savePayment($payment);
        }

        $this->addFlash('danger', 'Votre commande a échouée, nous vous invitons à réessayer !');
        return $this->render('payment/index.html.twig', [
            'order' => $order,
        ]);
    }
}
