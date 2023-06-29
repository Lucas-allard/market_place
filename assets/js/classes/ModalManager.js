class ModalManager {
    constructor(target, modal, backdrop, closeButton) {
        this.target = target;
        this.modal = modal;
        this.backdrop = backdrop;
        this.closeButton = closeButton;
    }

    open() {
        this.target.addEventListener('click', () => {
            this.modal.classList.add('open');
            this.backdrop.classList.add('open');
        });
    }

    close() {
        this.closeButton.addEventListener('click', () => {
            this.modal.classList.remove('open');
            this.backdrop.classList.remove('open');
        });
    }

    closeOnBackdrop() {
        this.backdrop.addEventListener('click', () => {
            this.modal.classList.remove('open');
            this.backdrop.classList.remove('open');
        });
    }

    closeOnEscape() {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.modal.classList.remove('open');
                this.backdrop.classList.remove('open');
            }
        });
    }

    init() {
        this.open();
        this.close();
        this.closeOnBackdrop();
        this.closeOnEscape();
    }

    static initAll() {
        const modals = document.querySelectorAll('.modal');
        const backdrops = document.querySelectorAll('.backdrop');
        const closeButtons = document.querySelectorAll('.modal-close');

        modals.forEach((modal, index) => {
            const target = document.querySelector(`[data-target="#${modal.id}"]`);
            const backdrop = backdrops[index];
            const closeButton = closeButtons[index];
            const modalInstance = new ModalManager(target, modal, backdrop, closeButton);
            modalInstance.init();
        });
    }
}

export default ModalManager;