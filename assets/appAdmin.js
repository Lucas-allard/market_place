import './bootstrap';


import './styles/appAdmin.scss';

import SelectManager from "./js/classes/SelectManager";
import DropdownManager from './js/classes/DropdownManager.js';
import TableActionManager from "./js/classes/TableActionManager";
import {captureFlashMessages} from "./js/utils/captureFlashMessage";


window.onload = () => {
    SelectManager.addSelects(document.querySelectorAll('.tom-select'));
    DropdownManager.init();
    TableActionManager.addTableActions(document.querySelectorAll('.crud-table'));
    captureFlashMessages();
}