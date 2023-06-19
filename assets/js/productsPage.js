import noUiSlider from 'nouislider';
function getMinMaxValue(minInput, maxInput) {
    return [parseInt(minInput.min), parseInt(maxInput.max)];
}
function createRangeSlider(range, minInput, maxInput) {
    const minMaxValue = getMinMaxValue(minInput, maxInput);

    noUiSlider.create(range, {
        range: {
            'min': minMaxValue[0],
            'max': minMaxValue[1]
        },
        step: 10,
        start: [0, 1000],
        margin: 0,
        limit: null,
        connect: true,
        direction: 'ltr',
        orientation: 'horizontal',
        behaviour: 'tap-drag',
        tooltips: true,
        format: {
            to: function (value) {
                return Math.round(value);
            },
            from: function (value) {
                return Math.round(value);
            }
        }

    });

    range.noUiSlider.on('update', function (values, handle) {
        minInput.value = Math.round(values[0]);
        maxInput.value = Math.round(values[1]);
    })
}
function createMoreButton(inputs, label) {
    const moreButtonContainer = document.createElement('div');
    moreButtonContainer.classList.add('flex', 'w-full');
    const moreButton = document.createElement('button');
    moreButton.type = 'button';
    moreButton.textContent = label;
    moreButton.classList.add('underline', 'text-custom-blue', 'text-xs', 'mt-2', 'block', 'cursor-pointer', 'hover:text-orange-400');
    moreButtonContainer.append(moreButton);

    inputs[inputs.length - 1].parentNode.parentNode.append(moreButtonContainer)
    return moreButton;
}
function displayInputs(inputs, initialVisibleInputs, moreButton) {
    const hideClass = 'hidden';
    const totalInputs = inputs.length;
    let visibleInputs = initialVisibleInputs;

    function hideAllInputs() {
        inputs.forEach(function (input) {
            input.parentElement.classList.add(hideClass);
        });
    }

    function showVisibleInputs() {
        inputs.forEach(function (input, index) {
            if (index < visibleInputs) {
                input.parentElement.classList.remove(hideClass);
            } else {
                input.parentElement.classList.add(hideClass);
            }
        });
    }

    hideAllInputs();
    showVisibleInputs();

    moreButton.addEventListener('click', function () {
        if (visibleInputs >= totalInputs) {
            // Show initial visible inputs and update button text
            visibleInputs = initialVisibleInputs;
            showVisibleInputs();
            moreButton.innerHTML = 'Voir plus';
        } else {
            // Show all inputs and update button text
            visibleInputs = totalInputs;
            showVisibleInputs();
            moreButton.innerHTML = 'Voir moins';
        }
    });
}
function changeUrlParam(url, param, value) {
    const urlObject = new URL(url);
    urlObject.searchParams.set(param, value);
    return urlObject.href;
}
function updateClientUrl(url) {
    window.history.pushState({}, '', url);
}
async function getProducts(url) {
    return await fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
    }).then(response => response.text());
}
async function updateProductList(param, value) {
    const url = changeUrlParam(window.location.href, param, value);

    try {
        const data = await getProducts(url);

        console.log(data)

        const parser = new DOMParser();
        const newDocument = parser.parseFromString(data, 'text/html');
        const newProducts = newDocument.querySelector('.products-list');

        document.querySelector('.products-list').replaceWith(newProducts);

        updateClientUrl(url);
    } catch (error) {
        console.log(error)
    }
}

window.addEventListener('load', () => {
    const form = document.querySelector('form[name="filter_form"]')
    const range = document.getElementById('range');
    const minInput = document.getElementById('filter_form_price_min');
    const maxInput = document.getElementById('filter_form_price_max');
    const orderFilter = document.getElementById('filter_order');
    const showFilter = document.getElementById('filter_show');

    createRangeSlider(range, minInput, maxInput);

    const brandInputs = form.querySelectorAll('input[name="filter_form[brand][]"]');
    let maxBrandInputs = 5;

    const caracteristicTypes = form.querySelectorAll('.caracteristic-type');
    let maxCaracteristicTypes = 1;

    const moreBrandButton = createMoreButton(brandInputs, 'Voir plus');
    const moreCaracteristicButton = createMoreButton(caracteristicTypes, 'Voir plus');

    displayInputs(brandInputs, maxBrandInputs, moreBrandButton);
    displayInputs(caracteristicTypes, maxCaracteristicTypes, moreCaracteristicButton);

    orderFilter.addEventListener('change', async function (e) {
        await updateProductList('order', this.value);
    });

    showFilter.addEventListener('change', async function (e) {
        await updateProductList('limit', this.value);
    });
});
