<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Product;
use App\Form\RegistrationForm\CheckoutRegistrationFormType;
use App\Form\UserForm\DynamicUserFormType;
use App\Manager\CartManager;
use App\Service\Form\FormProcessor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\UserAuthenticator;


#[Route('/panier', name: 'app_cart')]
class CartController extends AbstractController
{
    /**
     * @var CartManager
     */
    private CartManager $cartManager;
    /**
     * @var FormProcessor
     */
    private FormProcessor $formProcessor;

    /**
     * @param CartManager $cartManager
     * @param FormProcessor $formProcessor
     */
    public function __construct(CartManager $cartManager, FormProcessor $formProcessor)
    {
        $this->cartManager = $cartManager;
        $this->formProcessor = $formProcessor;
    }

    /**
     * @param Request $request
     * @param UserAuthenticatorInterface $userAuthenticator
     * @param UserAuthenticator $authenticator
     * @return Response
     */
    #[Route('/', name: '_index')]
    public function index(Request $request, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator): Response
    {
        $cart = $this->cartManager->getCurrentCart();

        if ($this->getUser() && $cart->getCustomer() !== $this->getUser()) {
            $cart->setCustomer($this->getUser());
            $this->cartManager->saveCart($cart);
        }

        $form = $this->getUser() ?
            $this->formProcessor->create(DynamicUserFormType::class, $this->getUser(), ['notBirthDate' => true, 'notEmail' => true])
            : $this->formProcessor->create(CheckoutRegistrationFormType::class, new Customer());

        if ($this->formProcessor->process($request, $form)) {
            if (!$this->getUser()) {
                $user = $form->getData();
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

    /**
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    #[Route('/ajouter/{id}', name: '_add', methods: ['POST'])]
    public function add(Request $request, Product $product): Response
    {
        if ($this->getUser() && !$this->isGranted("ROLE_CUSTOMER")) {
            return $this->json([
                'status' => "error",
                'message' => 'Vous devez être connecté en tant que client pour ajouter un produit au panier'
            ], 403);
        }

        $token = $request->headers->get('X-CSRF-TOKEN');

        if (!$this->isCsrfTokenValid('add-item', $token)) {
            return $this->json([
                'status' => "error",
                'message' => 'Le token est invalide'
            ], 400);
        }

        $quantity = json_decode($request->getContent(), true)['quantity'];

        $orderItem = $this->cartManager->getCartFactory()->createItem($product, $quantity);
        $orderItemSeller = $this->cartManager->getCartFactory()->createItemSeller($product);

        $cart = $this->cartManager->getCurrentCart();

        $cart->addOrderItem($orderItem)
            ->addOrderItemSeller($orderItemSeller)
            ->setCustomer($this->getUser());

        $this->cartManager->saveCart($cart);

        return $this->json([
            'status' => 'success',
            'message' => 'Le produit a bien été ajouté',
            'total' => $orderItem->getTotal()
        ]);
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    #[Route('/modifier/{id}', name: '_update', methods: ['PUT'])]
    public function update(Request $request, Product $product): Response
    {
        $token = $request->headers->get('X-CSRF-TOKEN');

        if ($this->isCsrfTokenValid('update-item', $token) === false) {
            return $this->json([
                'status' => "error",
                'message' => 'Le token est invalide'
            ], 400);
        }

        $quantity = json_decode($request->getContent(), true)['quantity'];

        $orderItem = $this->cartManager->getOrderItemByProduct($product);

        if ($orderItem === null) {
            return $this->json([
                'status' => "error",
                'message' => 'Le produit n\'existe pas dans le panier'
            ], 400);
        }

        $orderItem->setQuantity($quantity);


        $this->cartManager->saveCart($this->cartManager->getCurrentCart());

        return $this->json([
            'status' => 'success',
            'message' => 'Le produit a bien été modifié',
            'total' => $orderItem->getTotal()
        ]);
    }

    /**
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    #[Route('/supprimer/{id}', name: '_remove', methods: ['DELETE'])]
    public function remove(Request $request, Product $product): Response
    {
        $token = $request->headers->get('X-CSRF-TOKEN');

        if ($this->isCsrfTokenValid('remove-item', $token) === false) {
            return $this->json([
                'status' => "error",
                'message' => 'Le token est invalide'
            ], 400);
        }

        $cartItem = $this->cartManager->getOrderItemByProduct($product);
        $cartItemSeller = $this->cartManager->getOrderItemSellerBySeller($product->getSeller());

        if ($cartItem === null) {
            return $this->json([
                'status' => "error",
                'message' => 'Le produit n\'existe pas dans le panier'
            ], 400);
        }

        $cart = $this->cartManager->getCurrentCart();
        $cart->removeOrderItem($cartItem)
            ->removeOrderItemSeller($cartItemSeller);

        $this->cartManager->saveCart($cart);

        return $this->json([
            'status' => 'success',
            'message' => 'Le produit a bien été supprimé'
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/vider', name: '_clear', methods: ['DELETE'])]
    public function clear(Request $request): Response
    {
        $token = $request->headers->get('X-CSRF-TOKEN');

        if ($this->isCsrfTokenValid('clear-cart', $token) === false) {
            return $this->json([
                'status' => "error",
                'message' => 'Le token est invalide'
            ], 400);
        }

        $cart = $this->cartManager->getCurrentCart();
        $cart->removeOrderItems()
            ->removeOrderItemSellers();

        $this->cartManager->saveCart($cart);

        return $this->json([
            'status' => 'success',
            'message' => 'Le panier a bien été vidé'
        ]);
    }
}
