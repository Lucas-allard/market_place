class QuantityInput {
    constructor(inputElement, minusButton, plusButton, cartButtons) {
        this.inputElement = inputElement;
        this.quantityButtons = [minusButton, plusButton];
        this.quantity = this.inputElement.value;
        this.cartButtons = cartButtons;
    }

    updateQuantity() {

        this.quantityButtons.forEach(button => {
            button.addEventListener('click', () => {
                if (button.classList.contains('product-page-quantity-minus')) {
                    if (this.quantity > 1) {
                        this.quantity--;
                        this.inputElement.value = this.quantity;
                    }
                } else {
                    this.quantity++;
                    this.inputElement.value = this.quantity;
                }

                this.cartButtons.forEach(button => {
                    button.updateDataAttributes('data-quantity', this.quantity);

                });
            })
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

export default QuantityInput;