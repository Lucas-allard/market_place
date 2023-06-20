class ProductItem {
    constructor(id, name, price, quantity, picture) {
        this.id = id;
        this.name = name;
        this.price = parseFloat(price);
        this.quantity = parseInt(quantity);
        this.picture = {
            path: picture.path,
            alt: picture.alt
        };
    }
}

export default ProductItem;