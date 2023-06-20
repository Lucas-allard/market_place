import '../../styles/products/productPage.scss'
import Button from "../classes/Button";
import QuantityInput from "../classes/QuantityInput";

window.onload = function () {
    const addToCartButton = document.querySelectorAll('.add-to-cart');
    const quantityElement = document.querySelector("input[name='product-page-quantity']")
    const minusButton = document.querySelector('.product-page-quantity-minus')
    const plusButton = document.querySelector('.product-page-quantity-plus')
    const buttonsElements = [];

    addToCartButton.forEach(button => {
        const buttonElement = new Button({
            button: button,
            buttonLabel: 'Add to cart',
        });
        buttonsElements.push(buttonElement);
    })

    const quantityInput = new QuantityInput(quantityElement, minusButton, plusButton, buttonsElements);
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
}

