import '../../styles/pages/userPage/user.scss'
import '../../styles/pages/sellerPage/seller.scss'
import '../../styles/commons/crudTable.scss'
import '../../styles/commons/crudForm.scss'
import SelectManager from "../classes/SelectManager";
import collectionManager from "../classes/CollectionManager";

window.addEventListener('DOMContentLoaded', () => {
    SelectManager.addSelects(document.querySelectorAll('.tom-select'));

    new collectionManager('.crud-form', '.crud-collection', 'add-another-item')
});