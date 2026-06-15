(function () {
	'use strict';

	var EMAIL_SANITIZE_PATTERN = /[\u0400-\u04FF\u0500-\u052F\s,]/g;

	function sanitizeEmailValue(value) {
		return value.replace(EMAIL_SANITIZE_PATTERN, '').toLowerCase();
	}

	function normalizeEmailInput(input) {
		var sanitized = sanitizeEmailValue(input.value);

		if (sanitized !== input.value) {
			input.value = sanitized;
		}
	}

	function initEmailLowercase() {
		document.querySelectorAll('input[type="email"]').forEach(function (input) {
			if (input.closest('.contacts-page-form') || input.closest('.tour-checkout-form')) {
				return;
			}

			input.addEventListener('input', function () {
				normalizeEmailInput(input);
			});
		});
	}

	function init() {
		initEmailLowercase();
	}

	window.amalfitanaForms = {
		sanitizeEmailValue: sanitizeEmailValue,
		normalizeEmailInput: normalizeEmailInput
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
}());
