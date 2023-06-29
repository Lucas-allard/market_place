class Button {
    constructor({button = null, buttonLabel = null, lastElement = null}) {
        this.button = button;
        this.buttonLalbel = buttonLabel;
        this.lastElement = lastElement;
    }

    updateDataAttributes(attribute, value) {
        this.button.attributes[attribute].value = value;
    }

    createButton() {
        const buttonContainer = document.createElement('div');
        buttonContainer.classList.add('flex', 'w-full');

        this.button = document.createElement('button');
        this.button.type = 'button';
        this.updateButton(this.button, this.buttonLalbel)
        this.button.classList.add('underline', 'text-custom-blue', 'text-lg', 'mt-2', 'block', 'cursor-pointer', 'hover:text-orange-400');

        buttonContainer.append(this.button);
        this.lastElement[this.lastElement.length - 1].parentNode.parentNode.append(buttonContainer);
    }

    updateButton(button, label) {
        button.textContent = label;
    }
}

export default Button;