import SwiperSlider from "./SwiperSlider";

document.addEventListener("DOMContentLoaded", function () {
    new SwiperSlider(
        '.swiper .top-product__slider',
        {
            navigation: {
                nextEl: '.swiper-button-next-top-product',
                prevEl: '.swiper-button-prev-top-product',
            }
        }
    ).init();
})