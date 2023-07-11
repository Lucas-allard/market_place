import '../../styles/pages/userPage/user.scss'
import '../../styles/commons/pagination.scss'
import '../../styles/commons/crudTable.scss'
import '../../styles/commons/crudForm.scss'
import '../../styles/commons/datalist.scss'
import SelectManager from "../classes/SelectManager";
import collectionManager from "../classes/CollectionManager";
import TableActionManager from "../classes/TableActionManager";

window.addEventListener('DOMContentLoaded', () => {
    SelectManager.addSelects(document.querySelectorAll('.tom-select'));

    TableActionManager.addTableActions(document.querySelectorAll('.crud-table'));

    if (document.querySelector('.crud-form')) {
        new collectionManager('.crud-form', '.crud-collection', 'add-another-item')
    }

});