class Cart {
    constructor() {
        this.items = JSON.parse(localStorage.getItem('cart')) || [];
    }

    add(item) {
        this.items.push(item);
        this.save();
    }

    remove(index) {
        this.items.splice(index, 1);
        this.save();
    }

    reset() {
        this.items = [];
        this.save();
    }

    save() {
        localStorage.setItem('cart', JSON.stringify(this.items));

        const event = new CustomEvent('cartUpdated', {
            detail: {
                cart: this.items
            },
        });
        document.dispatchEvent(event);
    }

    getItems() {
        return this.items;
    }

    getTotal() {
        return this.items.reduce((total, item) => total + item.price, 0);
    }
}

export default Cart;