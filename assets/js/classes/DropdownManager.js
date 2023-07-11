class DropdownManager {
    constructor() {
        this.dropDownContainers = document.querySelectorAll('.dropdown');
        this.registerEvents();
    }

    registerEvents() {
        this.dropDownContainers.forEach((container) => {
            const dropDownIcon = container.querySelector('.dropdown-icon');
            const dropDownList = container.querySelector('.dropdown-menu');

            dropDownIcon.addEventListener('click', (event) => {
                event.stopPropagation()
                this.toggleDropDown(dropDownList, container);
            });
        });

        document.addEventListener('click', (event) => {
            this.closeAllDropDowns();
        });
    }

    toggleDropDown(dropdown, container) {
        if (dropdown.classList.contains('hidden')) {
            this.closeAllDropDowns();
            this.showDropDown(dropdown, container);
        } else {
            this.closeDropDown(dropdown, container);
        }
    }

    showDropDown(dropdown, container) {
        dropdown.classList.remove('hidden');
        container.classList.add('active');
    }

    closeDropDown(dropdown, container) {
        dropdown.classList.add('hidden');
        container.classList.remove('active');
    }

    closeAllDropDowns() {
        this.dropDownContainers.forEach((container) => {
            const dropDownList = container.querySelector('.dropdown-menu');
            this.closeDropDown(dropDownList, container);
        })
    }

    static init() {
        return new DropdownManager();
    }
}

export default DropdownManager;