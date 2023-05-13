import SwiperSlider from "./SwiperSlider";

document.addEventListener("DOMContentLoaded", function () {
    const topProductSlider = new SwiperSlider(
        '.swiper.top-product__slider',
        {
            navigation: {
                nextEl: '.swiper-button-next-top-product',
                prevEl: '.swiper-button-prev-top-product',
            }
        }
    ).init();

    const bestCategoriesSlider = new SwiperSlider(
        '.swiper.best-categories__slider',
        {
            autoplay: false,
            navigation: {
                nextEl: '.swiper-button-next-best-categories',
                prevEl: '.swiper-button-prev-best-categories',
            }
        }).init();
});