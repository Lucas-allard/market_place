import '../../styles/pages/singleProductPage/productPage.scss'
import Button from "../classes/Button";
import QuantityInputManager from "../classes/QuantityInputManager";

window.addEventListener('load', () => {
    const addToCartButton = document.querySelectorAll('.add-to-cart');
    const quantityElement = document.querySelector("input[name='product-page-quantity']")
    const minusButton = document.querySelector('.product-page-quantity.quantity-minus')
    const plusButton = document.querySelector('.product-page-quantity.quantity-plus')
    const buttonsElements = [];

    addToCartButton.forEach(button => {
        const buttonElement = new Button({
            button: button,
            buttonLabel: 'Add to cart',
        });
        buttonsElements.push(buttonElement);
    })

    const quantityInput = new QuantityInputManager(quantityElement, minusButton, plusButton, buttonsElements);
    quantityInput.updateQuantity();

    buttonsElements.forEach(({button}) => {
        button.addEventListener('click', () => {
            quantityInput.setQuantity(1);
            button.setAttribute('data-quantity', quantityInput.getQuantity());

            if (button.classList.contains('product-buy-now')) {
                window.location.href = button.getAttribute('data-url');
            }
        })
    })
})

