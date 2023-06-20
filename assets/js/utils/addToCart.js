import cart from "../../app.js";
import ProductItem from "../classes/ProductItem.js";

export function addItemToCart(buttonItem) {
    const productId = buttonItem.getAttribute('data-id');
    const productName = buttonItem.getAttribute('data-name');
    const productPrice = buttonItem.getAttribute('data-price');
    const productQuantity = buttonItem.getAttribute('data-quantity');
    const productPicturePath = buttonItem.getAttribute('data-picture-path');
    const productPictureAlt = buttonItem.getAttribute('data-picture-alt');

    console.log(productQuantity)
    const item = new ProductItem(
        productId,
        productName,
        productPrice,
        productQuantity,
        {
            productPicturePath,
            productPictureAlt
        });

    cart.add(item);
}
