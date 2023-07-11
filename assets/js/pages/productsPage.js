import RangeSliderManager from "../classes/RangeSliderManager";
import Button from "../classes/Button";
import InputManager from "../classes/InputManager";
import '../../styles/pages/productsPage/productsPage.scss';

window.addEventListener('load', () => {
    const form = document.querySelector('form[name="filter_form"]')
    const range = document.getElementById('range');
    const minInput = document.getElementById('filter_form_price_min');
    const maxInput = document.getElementById('filter_form_price_max');
    const brandInputs = form.querySelectorAll('input[name="filter_form[brand][]"]');
    const caracteristicTypes = form.querySelectorAll('.caracteristic-type');
    let maxCaracteristicTypes = 1;
    let maxBrandInputs = 5

    const rangeSlider = new RangeSliderManager(range, minInput, maxInput);

    const moreBrandButton = new Button({buttonLabel: 'Voir plus', lastElement: brandInputs});
    const moreCaracteristicButton = new Button({buttonLabel: 'Voir plus', lastElement: caracteristicTypes});
    moreBrandButton.createButton();
    moreCaracteristicButton.createButton();

    const brandInputsDisplay = new InputManager(brandInputs, maxBrandInputs);

    const caracteristicTypesDisplay = new InputManager(caracteristicTypes, maxCaracteristicTypes);

    brandInputsDisplay.displayInputs(moreBrandButton);
    caracteristicTypesDisplay.displayInputs(moreCaracteristicButton);
})
;
