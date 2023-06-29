<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Product;
use App\Form\RegistrationForm\CheckoutRegistrationFormType;
use App\Form\UserForm\UserAddressFormType;
use App\Manager\CartManager;
use App\Service\Form\FormProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\UserAuthenticator;

#[Route('/panier', name: 'app_cart')]
class CartController extends AbstractController
{
    private CartManager $cartManager;
    private FormProcessor $formProcessor;
    public function __construct(CartManager $cartManager, FormProcessor $formProcessor)
    {
        $this->cartManager = $cartManager;
        $this->formProcessor = $formProcessor;
    }

    #[Route('/', name: '_index')]
    public function index(Request $request, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator): Response
    {
        $cart = $this->cartManager->getCurrentCart();

        if ($this->getUser() && $cart->getCustomer() !== $this->getUser()) {
            $cart->setCustomer($this->getUser());
            $this->cartManager->saveCart($cart);
        }

        $form = $this->getUser() ?
            $this->formProcessor->create(UserAddressFormType::class, $this->getUser())
            : $this->formProcessor->create(CheckoutRegistrationFormType::class, new Customer());

        $this->formProcessor->handleRequest($request, $form);

        if ($this->formProcessor->isValid($form)) {
            if (!$this->getUser()) {
                $user = $form->getData();
                $password = $form->get('plainPassword')->getData();
                $user->setPassword($password);
                $this->formProcessor->save($user);
                $userAuthenticator->authenticateUser(
                    $user,
                    $authenticator,
                    $request
                );
            }

            return $this->redirectToRoute('app_payment_index');
        }

        $this->cartManager->saveCart($cart);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
            'form' => $form->createView()
        ]);
    }

    #[Route('/ajouter/{id}', name: '_add', methods: ['POST'])]
    public function add(Request $request, Product $product): Response
    {
        $token = $request->headers->get('X-CSRF-TOKEN');

        if (!$this->isCsrfTokenValid('add-item', $token)) {
            return $this->jsonResponse(false, 'Le token est invalide');
        }

        $quantity = json_decode($request->getContent(), true)['quantity'];

        $orderItem = $this->cartManager->getCartFactory()->createItem($product, $quantity);

        $cart = $this->cartManager->getCurrentCart();
        $cart->addOrderItem($orderItem);
        $cart->setCustomer($this->getUser());

        $this->cartManager->saveCart($cart);

        return $this->jsonResponse(true, 'Le produit a bien été ajouté au panier');
    }

    #[Route('/modifier/{id}', name: '_update', methods: ['PUT'])]
    public function update(Request $request, Product $product): Response
    {
        $token = $request->headers->get('X-CSRF-TOKEN');

        if ($this->isCsrfTokenValid('update-item', $token) === false) {
            return $this->jsonResponse(false, 'Le token est invalide');
        }

        $quantity = json_decode($request->getContent(), true)['quantity'];

        $orderItem = $this->cartManager->getOrderItemByProduct($product);

        if ($orderItem === null) {
            return $this->jsonResponse(false, 'Le produit n\'existe pas dans le panier');
        }

        $orderItem->setQuantity($quantity);

        $this->cartManager->saveCart($this->cartManager->getCurrentCart());

        return $this->jsonResponse(true, 'Le produit a bien été modifié', $orderItem->getTotal());
    }

    #[Route('/supprimer/{id}', name: '_remove', methods: ['DELETE'])]
    public function remove(Request $request, Product $product): Response
    {
        $token = $request->headers->get('X-CSRF-TOKEN');

        if ($this->isCsrfTokenValid('remove-item', $token) === false) {
            return $this->jsonResponse(false, 'Le token est invalide');
        }

        $cartItem = $this->cartManager->getOrderItemByProduct($product);

        if ($cartItem === null) {
            return $this->jsonResponse(false, 'Le produit n\'existe pas dans le panier');
        }


        $cart = $this->cartManager->getCurrentCart();
        $cart->removeOrderItem($cartItem);

        $this->cartManager->saveCart($cart);

        return $this->jsonResponse(true, 'Le produit a bien été supprimé');
    }

    #[Route('/vider', name: '_clear', methods: ['DELETE'])]
    public function clear(Request $request): Response
    {
        $token = $request->headers->get('X-CSRF-TOKEN');

        if ($this->isCsrfTokenValid('clear-cart', $token) === false) {
            return $this->jsonResponse(false, 'Le token est invalide');
        }

        $cart = $this->cartManager->getCurrentCart();
        $cart->removeOrderItems();

        $this->cartManager->saveCart($cart);

        return $this->jsonResponse(true, 'Le panier a bien été vidé');
    }

    private function jsonResponse(bool $success, string $message, mixed $data = null): JsonResponse
    {
        return new JsonResponse([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
    }
}
