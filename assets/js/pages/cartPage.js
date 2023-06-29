import "../../styles/pages/cartPage/cart.scss";
import QuantityInput from "../classes/QuantityInput";
import Fetch from "../classes/Fetch";
import swal from 'sweetalert';


function calculateTotalPrice(quantity, price) {
    return quantity * price;
}

function updateTotal(className) {
    const cartItems = document.querySelectorAll('.cart-item');
    let total = 0;

    cartItems.forEach((cartItem) => {
        const quantity = cartItem.querySelector('input[name="cart-page-quantity"]').value;
        const price = cartItem.querySelector('.cart-item-price-new').textContent.replace('€', '').replace(',', '.');

        const itemTotalPrice = cartItem.querySelector('.cart-item-total');
        const newTotalPrice = calculateTotalPrice(quantity, price).toFixed(2);
        itemTotalPrice.textContent = newTotalPrice.toString().replace('.', ',') + ' €';

        total += parseFloat(newTotalPrice);
    });

    const cartTotal = document.querySelector(className);
    cartTotal.textContent = total.toFixed(2).replace('.', ',') + ' €';
}


// Use the 'cart' instance as needed in your cartPage.js module
window.addEventListener('load', () => {
    const minusButtons = document.querySelectorAll('.cart-page-quantity.quantity-minus')
    const plusButtons = document.querySelectorAll('.cart-page-quantity.quantity-plus')
    const quantityButtons = [minusButtons, plusButtons];
    const deleteButtons = document.querySelectorAll('.cart-item-remove-button');
    const clearCartButton = document.querySelector('.cart-page-clear-cart-button');
    const checkoutButton = document.querySelector('.cart-page-checkout-button');
    const billingForm = document.querySelector('.cart-page-billing-form');

    QuantityInput.updateAllQuantities();

    quantityButtons.forEach(buttons => {
        buttons.forEach(button => {
            button.addEventListener('click', async () => {
                const csrfToken = button.getAttribute('data-token');
                const cartUrl = new URL(button.getAttribute('data-url'), window.location.origin);

                const quantity = button.getAttribute('data-quantity');
                const price = button.getAttribute('data-price');

                const itemTotalPrice = button.parentElement.parentElement.parentElement.querySelector('.cart-item-total');
                const initialTotalPrice = itemTotalPrice.innerHTML;
                const newTotalPrice = calculateTotalPrice(quantity, price).toFixed(2)
                itemTotalPrice.innerHTML = newTotalPrice.toString().replace('.', ',') + ' €';

                updateTotal('.cart-checkout-subtotal');
                updateTotal('.cart-checkout-total');

                const response = await Fetch.send(
                    cartUrl,
                    'PUT',
                    {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }, {
                        quantity: quantity,
                    }).then(response => response)
                if (!response.success) {
                    itemTotalPrice.innerHTML = initialTotalPrice;

                }
            })
        })
    })

    deleteButtons.forEach(button => {

        button.addEventListener('click', async () => {
            const csrfToken = button.getAttribute('data-token');
            const cartUrl = new URL(button.getAttribute('data-url'), window.location.origin);

            const cartItem = button.parentElement.parentElement
            const cartItemParent = cartItem.parentElement;

            cartItem.remove();

            const response = await Fetch.send(
                cartUrl,
                'DELETE',
                {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }).then(response => response)

            if (response.success) {
                await swal({
                    title: "Produit supprimé du panier",
                    icon: "success",
                    button: "OK",
                });
            }
            if (!response.success) {
                cartItemParent.append(cartItem);

                await swal({
                    title: "Une erreur est survenue",
                    text: "Le produit n'a pas pu être supprimé du panier",
                    icon: "error",
                    button: "OK",
                });
            }
        })
    })

    clearCartButton.addEventListener('click', async () => {
        const csrfToken = clearCartButton.getAttribute('data-token');
        const cartUrl = new URL(clearCartButton.getAttribute('data-url'), window.location.origin);

        const cartItems = document.querySelectorAll('.cart-item');
        const cartItemsParent = document.querySelector('.cart-items');

        cartItems.forEach(cartItem => {
            cartItem.remove();
        })

        const response = await Fetch.send(
            cartUrl,
            'DELETE',
            {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }).then(response => response)

        if (response.success) {
            await swal({
                title: "Panier vidé",
                icon: "success",
                button: "OK",
            });
        }

        if (!response.success) {
            for (const cartItem of cartItems) {
                cartItemsParent.append(cartItem);

                await swal({
                    title: "Une erreur est survenue",
                    text: "Le panier n'a pas pu être vidé",
                    icon: "error",
                    button: "OK",
                });
            }
        }
    });
})