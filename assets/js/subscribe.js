(function () {
	'use strict';

	var EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	var SANITIZE_PATTERN = /[\u0400-\u04FF\u0500-\u052F\s,]/g;
	var RESET_DELAY = 3500;

	var MESSAGES = {
		empty: 'Будь ласка, введіть ваш email.',
		invalid: 'Некоректний формат email (наприклад: name@mail.com).',
		success: 'Дякуємо! Ваш email успішно додано.'
	};

	function sanitizeEmailInput(value) {
		return value.replace(SANITIZE_PATTERN, '').toLowerCase();
	}

	function isValidEmail(value) {
		return EMAIL_PATTERN.test(value);
	}

	function initSubscribeForm(form) {
		var input = form.querySelector('.subscribe-section__input');
		var message = form.querySelector('.subscribe-section__message');
		var button = form.querySelector('[type="submit"]');
		var buttonText = form.querySelector('.subscribe-section__btn-text');
		var buttonIcon = button ? button.querySelector('.btn__icon') : null;
		var defaultButtonText = buttonText ? buttonText.textContent : 'Підписатися';
		var resetTimer = null;

		if (!input || !message || !button || !buttonText) {
			return;
		}

		function clearMessage() {
			message.textContent = '';
			message.classList.remove('is-error', 'is-success');
		}

		function setMessage(text, state) {
			message.textContent = text;
			message.classList.remove('is-error', 'is-success');

			if (state) {
				message.classList.add(state);
			}
		}

		function clearValidationState() {
			input.classList.remove('is-invalid', 'is-success');
		}

		function resetForm() {
			if (resetTimer) {
				clearTimeout(resetTimer);
				resetTimer = null;
			}

			input.value = '';
			clearValidationState();
			clearMessage();
			button.disabled = false;
			buttonText.textContent = defaultButtonText;

			if (buttonIcon) {
				buttonIcon.style.display = '';
			}
		}

		function showError(text) {
			if (resetTimer) {
				clearTimeout(resetTimer);
				resetTimer = null;
			}

			input.classList.remove('is-success');
			input.classList.add('is-invalid');
			setMessage(text, 'is-error');
			button.disabled = false;
			buttonText.textContent = defaultButtonText;

			if (buttonIcon) {
				buttonIcon.style.display = '';
			}
		}

		function showSuccess() {
			input.classList.remove('is-invalid');
			input.classList.add('is-success');
			setMessage(MESSAGES.success, 'is-success');
			button.disabled = true;
			buttonText.textContent = 'Готово! ✓';

			if (buttonIcon) {
				buttonIcon.style.display = 'none';
			}

			resetTimer = setTimeout(resetForm, RESET_DELAY);
		}

		input.addEventListener('input', function () {
			var sanitized = sanitizeEmailInput(input.value);

			if (sanitized !== input.value) {
				input.value = sanitized;
			}

			clearValidationState();
			clearMessage();
		});

		form.addEventListener('submit', function (event) {
			event.preventDefault();

			var value = sanitizeEmailInput(input.value);

			if (value !== input.value) {
				input.value = value;
			}

			if (!value) {
				showError(MESSAGES.empty);
				return;
			}

			if (!isValidEmail(value)) {
				showError(MESSAGES.invalid);
				return;
			}

			showSuccess();
		});
	}

	function initSubscribeForms() {
		document.querySelectorAll('.subscribe-section__form:not(.tour-checkout-form)').forEach(initSubscribeForm);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initSubscribeForms);
	} else {
		initSubscribeForms();
	}
})();
