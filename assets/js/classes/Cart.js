class Cart {
    constructor() {
        this.items = JSON.parse(localStorage.getItem('cart')) || [];
    }

    add(item) {
        console.log(item)
        if (this.cartHasItem(item)) {
            const index = this.getItemIndex(item);
            console.log(typeof this.items[index].totalPrice)
            console.log(typeof item.price)
            this.items[index].quantity++;
            this.items[index].totalPrice = (parseFloat(this.items[index].totalPrice) + item.price).toFixed(2);
            this.save();
            return;
        }

        this.items.push(item);
        this.save();
    }

    remove(item) {
        if (!this.cartHasItem(item)) {
            return;
        }

        const index = this.getItemIndex(item);
        this.items[index].quantity--;
        this.items[index].totalPrice = (this.items[index].totalPrice - item.price).toFixed(2);

        if (this.items[index].quantity === 0) {
            this.items.splice(index, 1);
        }

        this.save();
    }

   delete(item) {
        if (!this.cartHasItem(item)) {
            return;
        }

        const index = this.getItemIndex(item);
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

    getItemIndex(item) {
        return this.items.findIndex((i) => i.id === item.id);
    }

    cartHasItem(item) {
        return this.items.some((i) => i.id === item.id);
    }
}

export default Cart;