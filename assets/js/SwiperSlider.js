import Swiper, {Autoplay, Navigation} from 'swiper';
// import Swiper styles
import 'swiper/css';

// configure Swiper to use modules
Swiper.use([Autoplay, Navigation]);

export default class SwiperSlider {
    constructor(selector, options) {
        this.selector = selector;
        this.options = {
            direction: 'horizontal',
            slidesPerView: 1,
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: true,
            },
            speed: 1000,
            navigation: {
                nextEl: '.swiper-button-next-unique',
                prevEl: '.swiper-button-prev-unique',
            },
            ...options
        }
    }

    init() {
        return new Swiper(this.selector, this.options);

    }
}
