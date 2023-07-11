import TomSelect from "tom-select";
import 'tom-select/dist/css/tom-select.min.css'; // Importez le fichier CSS de Tom

class SelectManager {
    static addSelect(select) {
        new TomSelect(select, {
            create: false,
            sortField: {
                field: 'text',
                direction: 'asc'
            }
        });
    }

    static addSelects(selects) {
        selects.forEach(select => {
            this.addSelect(select);
        });
    }
}

export default SelectManager;