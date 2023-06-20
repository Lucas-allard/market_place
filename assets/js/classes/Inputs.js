import Button from "./Button";

class Inputs {
    constructor(inputElements, visibleInputElements) {
        this.hideClass = 'hidden';
        this.inputElements = inputElements;
        this.initialVisibleInputElements = visibleInputElements;
        this.visibleInputElements = visibleInputElements;
        this.totalElements = this.inputElements.length;
    }

    showInput() {
        this.inputElements.forEach((input, index) => {
            if (index < this.visibleInputElements) {
                input.parentElement.classList.remove(this.hideClass);
            } else {
                input.parentElement.classList.add(this.hideClass);
            }
        });
    }

    showAllInputs() {
        this.visibleInputElements = this.totalElements;
        this.showInput();
    }

    showInitialInputs() {
        this.visibleInputElements = this.initialVisibleInputElements;
        this.showInput();
    }


    addMoreInputs(button) {
        button.button.addEventListener('click', () => {
            console.log('click')
            if (this.visibleInputElements >= this.totalElements) {
                this.showInitialInputs();
                button.updateButton(button.button, 'Voir plus');
            } else {
                this.showAllInputs();
                button.updateButton(button.button, 'Voir moins');
            }
        });
    }

    displayInputs(button) {
        this.showInput();

        if (this.totalElements > this.initialVisibleInputElements) {
            const button = new Button(this.inputElements, 'Voir plus');
        }

        this.addMoreInputs(button);

        return button;
    }
}

export default Inputs;