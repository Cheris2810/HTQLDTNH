
document.addEventListener('DOMContentLoaded', function() {
    new Swiper('.mySwiper', {
      slidesPerView: 1,
      spaceBetween: 10,
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
      autoplay: {
        delay: 3000,
        disableOnInteraction: false,
      },
      speed: 1000, // Tốc độ chuyển động (1 giây)
      loop: true, // Lặp lại carousel
      grabCursor: true, // Thay đổi con trỏ thành "grab" khi nắm bắt carousel
      effect: 'fade', // Hiệu ứng chuyển động (slide, fade, cube, coverflow, flip)
      fadeEffect: {
        crossFade: true, // Hiệu ứng mờ dần khi chuyển đổi slide
      },
    });
    });
    