import Button from "./Button";

class QuantityInputManager {
    constructor(inputElement, minusButton, plusButton, cartButtons) {
        this.inputElement = inputElement;
        this.quantityButtons = [minusButton, plusButton];
        this.quantity = this.inputElement.value;
        this.cartButtons = cartButtons;
    }

    updateQuantity() {

        this.quantityButtons.forEach(button => {
            button.addEventListener('click', () => {
                if (button.classList.contains('quantity-minus')) {
                    if (this.quantity > 1) {
                        this.quantity--;
                        this.inputElement.value = this.quantity;
                    }
                } else {
                    this.quantity++;
                    this.inputElement.value = this.quantity;
                }

                if (this.cartButtons !== null) {
                    this.cartButtons.forEach(button => {
                        if (button.button.getAttribute('data-product-id') === this.inputElement.getAttribute('data-product-id')) {
                            button.updateDataAttributes('data-quantity', this.quantity);
                        }
                    });
                }
            })
        })
    }

    static updateAllQuantities() {

        const quantityElement = document.querySelectorAll("input[name='cart-page-quantity']")
        const minusButton = document.querySelectorAll('.cart-page-quantity.quantity-minus')
        const plusButton = document.querySelectorAll('.cart-page-quantity.quantity-plus')
        const actionButton = [...minusButton, ...plusButton];
        const cartButtons = [];

        actionButton.forEach(button => {
            const buttonElement = new Button({button: button});
            cartButtons.push(buttonElement);
        })

        quantityElement.forEach((element, index) => {
            (new QuantityInputManager(element, minusButton[index], plusButton[index], cartButtons)).updateQuantity();
        })
    }

    getQuantity() {
        return this.quantity;
    }

    setQuantity(quantity) {
        this.quantity = quantity;

        if (quantity === 1) {
            this.setInputElementValue(quantity);
        }
    }

    getInputElement() {
        return this.inputElement;
    }

    setInputElementValue(value) {
        this.inputElement.value = value;
    }
}

export default QuantityInputManager;