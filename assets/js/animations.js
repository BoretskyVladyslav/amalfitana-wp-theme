(function () {
	'use strict';

	if (!('IntersectionObserver' in window)) {
		document.querySelectorAll('.animate-on-scroll').forEach(function (element) {
			element.classList.add('is-animated');
		});
		return;
	}

	var observer = new IntersectionObserver(
		function (entries, io) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) {
					return;
				}

				entry.target.classList.add('is-animated');
				io.unobserve(entry.target);
			});
		},
		{
			threshold: 0.1,
		}
	);

	document.querySelectorAll('.animate-on-scroll').forEach(function (element) {
		observer.observe(element);
	});
}());
