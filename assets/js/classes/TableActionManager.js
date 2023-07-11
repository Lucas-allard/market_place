import Fetch from "./Fetch";

class TableActionManager {
    constructor(table) {
        this.table = table;
        this.deleteActions = table.querySelectorAll('.action-delete');
        this.editActions = table.querySelectorAll('.action-edit');
        this.init();
    }

    init() {
        this.attachDeleteEventListeners();
        this.attachEditEventListeners();
    }

    attachDeleteEventListeners() {
        this.deleteActions.forEach(action => {
            action.addEventListener('click', (e) => {
                e.preventDefault();

                this.handleDelete(action);
            });
        });
    }

    attachEditEventListeners() {
        this.editActions.forEach(action => {
            action.addEventListener('click', (e) => {
                e.preventDefault();

                this.handleEdit(action);
            });
        });
    }

    async handleDelete(action) {
        const userResponse = await swal({
            title: 'Êtes-vous sûr?',
            text: 'Cette action est irréversible, vous ne pourrez pas revenir en arrière!',
            icon: 'warning',
            buttons: ['Annuler', 'Supprimer'],
            dangerMode: true,
        });

        if (!userResponse) {
            return;
        }

        const {csrfToken, url, row} = this.getTableRowInfo(action);

        row.remove();

        const response = await Fetch.send(
            url,
            'DELETE',
            {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        ).then(response => response);


        if (response.status !== "success") {
            this.table.append(row);
        }

        await swal({
            title: response.message,
            icon: response.status,
            button: 'OK',
        });
    }

    async handleEdit(action) {
        const {url} = this.getTableRowInfo(action);

        window.location.href = url;
    }

    getTableRowInfo(action) {
        const row = action.closest('tr');
        const csrfToken = action.getAttribute('data-token');
        const url = new URL(action.href, window.location.origin);

        return {csrfToken, url, row};
    }

    static addTableActions(tables) {
        tables.forEach(table => {
            new TableActionManager(table);
        });
    }

    updateRow(row, data) {
        switch (data.type) {
            case 'ship':
                row.querySelector('.shipping-date').textContent = data.date;
                row.querySelector('.order-status').textContent = data.status;
        }
    }
}

export default TableActionManager;