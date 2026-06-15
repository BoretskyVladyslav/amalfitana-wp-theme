(function () {
	'use strict';

	function closeItem(item) {
		if (!item) {
			return;
		}

		item.classList.remove('faq-item--expanded');

		const trigger = item.querySelector('.faq-item__trigger');

		if (trigger) {
			trigger.setAttribute('aria-expanded', 'false');
		}
	}

	function openItem(item) {
		if (!item) {
			return;
		}

		item.classList.add('faq-item--expanded');

		const trigger = item.querySelector('.faq-item__trigger');

		if (trigger) {
			trigger.setAttribute('aria-expanded', 'true');
		}
	}

	function initFaqAccordion() {
		const containers = document.querySelectorAll('.faq-section, .faq-accordion-container');

		if (!containers.length) {
			return;
		}

		containers.forEach(function (faqSection) {
			const triggers = faqSection.querySelectorAll('.faq-item__trigger');

			triggers.forEach(function (trigger) {
				trigger.addEventListener('click', function () {
					const item = trigger.closest('.faq-item');

					if (!item) {
						return;
					}

					const isExpanded = item.classList.contains('faq-item--expanded');

					faqSection.querySelectorAll('.faq-item--expanded').forEach(function (openItem) {
						if (openItem !== item) {
							closeItem(openItem);
						}
					});

					if (isExpanded) {
						closeItem(item);
					} else {
						openItem(item);
					}
				});
			});
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initFaqAccordion);
	} else {
		initFaqAccordion();
	}
})();
