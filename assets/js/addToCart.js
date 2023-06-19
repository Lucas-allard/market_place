import Cart from "./Cart";

window.onload = function() {
    const cart = new Cart();
    const addButtons = document.querySelectorAll('.add-to-cart');

    addButtons.forEach((button) => {
        button.addEventListener('click', (event) => {
            const item = {
                id: button.dataset.id,
                name: button.dataset.name,
                price: button.dataset.price,
            };

            cart.add(item);
        });
    });
}