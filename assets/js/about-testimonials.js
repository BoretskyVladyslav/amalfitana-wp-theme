document.addEventListener('DOMContentLoaded', function () {
	var swiperEl = document.querySelector('.about-testimonials__swiper');

	if (!swiperEl || typeof Swiper === 'undefined') {
		return;
	}

	new Swiper(swiperEl, {
		slidesPerView: 1,
		spaceBetween: 20,
		loop: true,
		navigation: {
			nextEl: '.about-testimonials__arrow--next',
			prevEl: '.about-testimonials__arrow--prev',
		},
		pagination: {
			el: '.about-testimonials__pagination',
			clickable: true,
			bulletClass: 'about-testimonials__dot',
			bulletActiveClass: 'is-active',
		},
		breakpoints: {
			768: {
				slidesPerView: 2,
				spaceBetween: 30,
			},
			1170: {
				slidesPerView: 3,
				spaceBetween: 30,
			},
		},
	});
});
