import SwiperSlider from "./SwiperSlider";
import Particles from "particlesjs";

document.addEventListener("DOMContentLoaded", function () {
    new SwiperSlider(
        '.swiper.brands__slider',
        {
            slidesPerView: 5,
            autoplay: {
                delay: 3000,
            },
            speed: 1000,
            spaceBetween: 20,
            breakpoints: {
                320: {
                    slidesPerView: 3,
                },
                390: {
                    slidesPerView: 4,

                },
                850: {
                    slidesPerView: 5,
                },
                1200: {
                    slidesPerView: 7,
                },
                1400: {
                    slidesPerView: 10,
                }
            },
        }
    ).init();

    new SwiperSlider(
        '.swiper.best-categories__slider',
        {
            autoplay: false,
            navigation: {
                nextEl: '.swiper-button-next-best-categories',
                prevEl: '.swiper-button-prev-best-categories',
            }
        }).init();

    new SwiperSlider(
        '.swiper.top-product__slider',
        {
            navigation: {
                nextEl: '.swiper-button-next-top-product',
                prevEl: '.swiper-button-prev-top-product',
            }
        }
    ).init();

    new SwiperSlider(
        '.swiper.top-brands__slider',
        {
            autoplay: {
                delay: 3000,
            },
            speed: 1000,
            navigation: {
                nextEl: '.swiper-button-next-top-brands',
                prevEl: '.swiper-button-prev-top-brands',
            }
        }
    ).init();

    Particles.init({
        selector: '.home-page__particles',
        color: '#ffffff',
        maxParticles: 200,
        speed: 0.5,
        sizeVariations: 3,
    });
});