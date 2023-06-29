class MenuManager {
    constructor(menus) {
        this.menus = menus;
        this.triggerElements = document.querySelectorAll('.menu-trigger');
        this.registerEvents();

    }

    registerEvents() {
        this.triggerElements.forEach((triggerElement) => {
            triggerElement.addEventListener('click', (event) => {
                event.stopPropagation();

                const menuId = triggerElement.getAttribute('data-menu-id');
                this.toggleMenu(menuId);
            });
        });
    }

    toggleMenu(menuId) {

        const menu = this.getMenu(menuId);
        if (menu.classList.contains('active')) {
            this.hideMenu(menu);
        } else {
            this.showMenu(menu);
        }
    }

    showMenu(menu) {
        menu.classList.add('active');
        if (menu.classList.contains('sidenav-container')) {
            this.toggleMainSection(menu.nextElementSibling);
        }
    }

    hideMenu(menu) {
        menu.classList.remove('active');
        if (menu.classList.contains('sidenav-container')) {
            this.toggleMainSection(menu.nextElementSibling);

        }
    }

    getMenu(menuId) {
        return this.menus.find((menu) => {
            return menu.id === menuId;
        });
    }

    toggleMainSection(mainSection) {
        mainSection.classList.toggle('mobile-sidebar-active');
    }

}

export default MenuManager;