/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

// Import Flowbite
import 'flowbite';

// Import addToCart function
import {addItemToCart} from './js/utils/addToCart.js';
import DropdownManager from './js/classes/DropdownManager.js';
import MenuManager from "./js/classes/MenuManager";
import ModalManager from "./js/classes/ModalManager";

window.onload = () => {
    const mobileMenu = document.getElementById('mobileMenu');
    const sidenavMenu = document.getElementById('sidenavMenu');
    const menuManager = new MenuManager([mobileMenu, sidenavMenu]);
    DropdownManager.init();
    ModalManager.initAll();


// Add event listeners for addToCart functionality
    const addButtons = document.querySelectorAll('.add-to-cart');
    addButtons.forEach((buttonItem) => {
        buttonItem.addEventListener('click', (event) => {
            addItemToCart(buttonItem);
        });
    });
}
