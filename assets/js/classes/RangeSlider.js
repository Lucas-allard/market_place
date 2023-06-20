import noUiSlider from 'nouislider';

class RangeSlider {
    constructor(rangeElement, minInput, maxInput) {
        this.rangeElement = rangeElement;
        this.minInput = minInput;
        this.maxInput = maxInput;
        this.range = this.createRangeSlider();
    }

    getMinMaxValue() {
        return [parseInt(this.minInput.min), parseInt(this.maxInput.max)];
    }

    createRangeSlider() {
        this.minMaxValue = this.getMinMaxValue();

        noUiSlider.create(this.rangeElement, {
            range: {
                'min': this.minMaxValue[0],
                'max': this.minMaxValue[1]
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
                    return Math.round(parseInt(value));
                }
            }
        });

        this.rangeElement.noUiSlider.on('update', this.updateInputs.bind(this));

        return this.rangeElement.noUiSlider;
    }
    updateInputs(values, handle) {
        this.minInput.value = Math.round(values[0]);
        this.maxInput.value = Math.round(values[1]);
    }
}

export default RangeSlider;