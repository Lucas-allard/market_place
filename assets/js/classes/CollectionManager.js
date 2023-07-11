class CollectionManager {
    constructor(formSelector, collectionSelector, addBtnId, maxItems = 4) {
        this.maxItems = maxItems;
        this.form = document.querySelector(formSelector);
        this.collectionContainers = this.form.querySelectorAll(collectionSelector);
        this.addBtn = document.getElementById(addBtnId);
        this.removeBtns = this.form.querySelectorAll('.crud-collection-item-remove button');

        this.addBtn.addEventListener('click', this.handleAddButtonClick.bind(this));

        this.removeBtns.forEach(removeBtn => {
            removeBtn.addEventListener('click', this.removeCollectionItem.bind(this, removeBtn));
        })

        this.collectionContainers.forEach(collection => {
            const collectionItems = collection.querySelectorAll('.crud-collection-item');
            if (collectionItems.length === 0) {
                this.addCollectionItem(collection);
            }
        });

        this.updateAddButtonVisibility();
    }
    handleAddButtonClick() {
        this.collectionContainers.forEach(collection => {
            const collectionItems = collection.querySelectorAll('.crud-collection-item');
            if (collectionItems.length < this.maxItems) {
                this.addCollectionItem(collection);
            }
        });

        // Mettre à jour la visibilité du bouton d'ajout
        this.updateAddButtonVisibility();
    }
    addCollectionItem(collection) {
        const prototype = collection.getAttribute('data-prototype');
        const index = collection.querySelectorAll('.crud-collection-item').length;
        const newForm = this.generateNewForm(prototype, index);
        const formContainer = collection.lastElementChild;


        const fieldContainer = this.createFieldContainer(newForm);
        const removeBtnContainer = this.createRemoveButtonContainer(fieldContainer);
        const removeBtn = this.createRemoveButton(removeBtnContainer, fieldContainer);

        this.insertCollectionItem(formContainer, fieldContainer);
    }
    generateNewForm(prototype, index) {
        return prototype.replace(/__name__/g, index);
    }
    createFieldContainer(newForm) {
        const fieldContainer = document.createElement('div');
        fieldContainer.classList.add('form-group');
        fieldContainer.innerHTML = newForm;
        fieldContainer.firstElementChild.classList.add('crud-collection-item');
        return fieldContainer;
    }
    createRemoveButtonContainer(fieldContainer) {
        const removeBtnContainer = document.createElement('div');
        removeBtnContainer.classList.add('crud-collection-item-remove');
        fieldContainer.appendChild(removeBtnContainer);
        return removeBtnContainer;
    }
    createRemoveButton(removeBtnContainer, fieldContainer) {
        const removeBtn = document.createElement('button');
        removeBtn.classList.add('button', 'button-red');
        removeBtn.innerHTML = '<i class="fas fa-trash"></i>';
        removeBtn.addEventListener('click', this.removeCollectionItem.bind(this, removeBtn));
        removeBtnContainer.appendChild(removeBtn);
        return removeBtn;
    }
    insertCollectionItem(formContainer, fieldContainer) {
        formContainer.insertAdjacentElement('afterend', fieldContainer);
    }
    updateAddButtonVisibility() {
        const collectionItemsCount = Array.from(this.collectionContainers).reduce((total, collection) => {
            const collectionItems = collection.querySelectorAll('.crud-collection-item');
            return total + collectionItems.length;
        }, 0);

        if (collectionItemsCount >= this.maxItems) {
            this.addBtn.style.display = 'none';
        } else {
            this.addBtn.style.display = 'block';
        }
    }

    removeCollectionItem(removeBtn) {
        const collectionPreview = removeBtn.parentElement.parentElement.nextElementSibling;
        if (collectionPreview) {
            collectionPreview.remove();
        }

        removeBtn.parentElement.parentElement.remove();
        this.updateAddButtonVisibility();
    }

}

export default CollectionManager;
