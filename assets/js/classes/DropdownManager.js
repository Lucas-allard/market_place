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
                this.toggleDropDown(dropDownList);
            });
        });

        document.addEventListener('click', (event) => {
            this.closeAllDropDowns();
        });
    }

    toggleDropDown(dropdown) {
        if (dropdown.classList.contains('hidden')) {
            this.closeAllDropDowns();
            this.showDropDown(dropdown);
        } else {
            this.closeDropDown(dropdown);
        }
    }

    showDropDown(dropdown) {
        dropdown.classList.remove('hidden');
    }

    closeDropDown(dropdown) {
        dropdown.classList.add('hidden');
    }

    closeAllDropDowns() {
        this.dropDownContainers.forEach((container) => {
            const dropDownList = container.querySelector('.dropdown-menu');
            this.closeDropDown(dropDownList);
        })
    }

    static init() {
        return new DropdownManager();
    }
}

export default DropdownManager;