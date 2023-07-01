import QuantityInputManager from "./QuantityInputManager";
import Fetch from "../classes/Fetch";
import swal from 'sweetalert';

class CartActionManager {
    constructor() {
        this.minusButtons = document.querySelectorAll('.cart-page-quantity.quantity-minus');
        this.plusButtons = document.querySelectorAll('.cart-page-quantity.quantity-plus');
        this.deleteButtons = document.querySelectorAll('.cart-item-remove-button');
        this.clearCartButton = document.querySelector('.cart-page-clear-cart-button');
        this.cartItems = document.querySelectorAll('.cart-item');

        this.init();
    }

    init() {
        QuantityInputManager.updateAllQuantities();
        this.attachQuantityEventListeners();
        this.attachDeleteEventListeners();
        this.attachClearCartEventListener();
    }

    attachQuantityEventListeners() {
        [this.minusButtons, this.plusButtons].forEach(buttons => {
            buttons.forEach(button => {
                button.addEventListener('click', this.handleQuantityChange.bind(this, button));
            });
        });
    }

    async handleQuantityChange(button) {
        const {csrfToken, cartUrl, quantity, price, itemTotalPrice} = this.getCartItemInfo(button);

        const initialTotalPrice = itemTotalPrice.innerHTML;
        const newTotalPrice = this.calculateTotalPrice(quantity, price).toFixed(2);
        itemTotalPrice.innerHTML = newTotalPrice.toString().replace('.', ',') + ' €';

        this.updateTotal('.cart-checkout-subtotal');
        this.updateTotal('.cart-checkout-total');

        const response = await Fetch.send(
            cartUrl,
            'PUT',
            {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            {
                quantity: quantity,
            }
        ).then(response => response);

        if (!response.success) {
            itemTotalPrice.innerHTML = initialTotalPrice;
        }
    }

    attachDeleteEventListeners() {
        this.deleteButtons.forEach(button => {
            button.addEventListener('click', this.handleDelete.bind(this, button));
        });
    }

    async handleDelete(button) {
        const {csrfToken, cartUrl, cartItem, cartItemParent} = this.getCartItemInfo(button);

        cartItem.remove();

        const response = await Fetch.send(
            cartUrl,
            'DELETE',
            {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        ).then(response => response);

        if (response.success) {
            await swal({
                title: "Produit supprimé du panier",
                icon: "success",
                button: "OK",
            });
        } else {
            cartItemParent.append(cartItem);

            await swal({
                title: "Une erreur est survenue",
                text: "Le produit n'a pas pu être supprimé du panier",
                icon: "error",
                button: "OK",
            });
        }
    }

    attachClearCartEventListener() {
        this.clearCartButton.addEventListener('click', this.handleClearCart.bind(this));
    }

    async handleClearCart() {
        const csrfToken = this.clearCartButton.getAttribute('data-token');
        const cartUrl = new URL(this.clearCartButton.getAttribute('data-url'), window.location.origin);
        const cartItemsParent = document.querySelector('.cart-items');

        this.cartItems.forEach(cartItem => {
            cartItem.remove();
        });

        const response = await Fetch.send(
            cartUrl,
            'DELETE',
            {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        ).then(response => response);

        if (response.success) {
            await swal({
                title: "Panier vidé",
                icon: "success",
                button: "OK",
            });
        } else {
            this.cartItems.forEach(cartItem => {
                cartItemsParent.append(cartItem);
            });

            await swal({
                title: "Une erreur est survenue",
                text: "Le panier n'a pas pu être vidé",
                icon: "error",
                button: "OK",
            });
        }
    }

    getCartItemInfo(button) {
        const csrfToken = button.getAttribute('data-token');
        const cartUrl = new URL(button.getAttribute('data-url'), window.location.origin);
        const cartItem = button.parentElement.parentElement;
        const cartItemParent = cartItem.parentElement;
        const quantity = button.getAttribute('data-quantity');
        const price = button.getAttribute('data-price');
        const itemTotalPrice = button.parentElement.parentElement.parentElement.querySelector('.cart-item-total');

        return {csrfToken, cartUrl, cartItem, cartItemParent, quantity, price, itemTotalPrice};
    }

    calculateTotalPrice(quantity, price) {
        return quantity * price;
    }

    updateTotal(className) {
        const cartItems = document.querySelectorAll('.cart-item');
        let total = 0;

        cartItems.forEach(cartItem => {
            const quantity = cartItem.querySelector('input[name="cart-page-quantity"]').value;
            const price = cartItem.querySelector('.cart-item-price-new').textContent.replace('€', '').replace(',', '.');

            const itemTotalPrice = cartItem.querySelector('.cart-item-total');
            const newTotalPrice = this.calculateTotalPrice(quantity, price).toFixed(2);
            itemTotalPrice.textContent = newTotalPrice.toString().replace('.', ',') + ' €';

            total += parseFloat(newTotalPrice);
        });

        const cartTotal = document.querySelector(className);
        cartTotal.textContent = total.toFixed(2).replace('.', ',') + ' €';
    }
}

export default CartActionManager;