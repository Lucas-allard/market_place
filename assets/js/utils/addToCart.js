import Fetch from "../classes/Fetch";
import swal from 'sweetalert';

export async function addItemToCart(buttonItem) {
    const csrfToken = buttonItem.getAttribute('data-token');
    const productQuantity = buttonItem.dataset.quantity
    const cartUrl = new URL(buttonItem.dataset.url, window.location.origin);

    // send the product id and quantity to the server
    // and add the product to the cart
    const response = await Fetch.send(
        cartUrl,
        'POST',
        {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        {
            quantity: productQuantity
        })
        .then(response =>response);

    if (response.success) {
        await swal({
            title: "Produit ajouté au panier",
            icon: "success",
            button: "OK",
        });
    }
    if (!response.success) {
        await swal({
            title: "Une erreur est survenue lors de l'ajout du produit au panier",
            icon: "error",
            button: "OK",
        });
    }
}
