// Fonction pour capturer les messages flash
import {Logger} from "sass";

export function captureFlashMessages() {
    const flashMessages = document.getElementsByClassName('alert');

    Array.from(flashMessages).forEach((message) => {
        const messageType = message.dataset.type;
        const messageText = message.textContent;

        message.remove();

// Affichage du message avec Swal.js
        swal({
            icon: messageType,
            text: messageText,
            showConfirmButton: false,
            timer: 3000
        })
    });
}


