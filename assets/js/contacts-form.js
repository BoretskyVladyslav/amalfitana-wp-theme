(function () {
	'use strict';

	var EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	var EMAIL_SANITIZE_PATTERN = /[\u0400-\u04FF\u0500-\u052F\s,]/g;
	var LOADING_DELAY = 800;
	var SUCCESS_RESET_DELAY = 3000;

	var MESSAGES = {
		nameEmpty: "Будь ласка, введіть ваше ім'я.",
		emailEmpty: 'Будь ласка, введіть ваш email.',
		emailInvalid: 'Некоректний формат email (наприклад: name@mail.com).',
		questionEmpty: 'Будь ласка, напишіть ваше питання.',
		questionTooShort: 'Питання занадто коротке. Напишіть детальніше.'
	};

	function sanitizeEmailInput(value) {
		return value.replace(EMAIL_SANITIZE_PATTERN, '').toLowerCase();
	}

	function isValidEmail(value) {
		return EMAIL_PATTERN.test(value);
	}

	function getControl(input) {
		return input ? input.closest('.contacts-page-form__field') : null;
	}

	function initContactsForm() {
		var form = document.querySelector('.contacts-page-form');

		if (!form) {
			return;
		}

		var nameInput = form.querySelector('#contacts-form-name');
		var emailInput = form.querySelector('#contacts-form-email');
		var questionInput = form.querySelector('#contacts-form-question');
		var message = form.querySelector('.contacts-page-form__message');
		var button = form.querySelector('#contacts-form-submit');
		var buttonText = form.querySelector('.contacts-page-form__btn-text');
		var buttonIcon = button ? button.querySelector('.btn__icon') : null;
		var defaultButtonText = buttonText ? buttonText.textContent : 'Задати питання';
		var loadingTimer = null;
		var resetTimer = null;
		var inputs = [nameInput, emailInput, questionInput];

		if (!nameInput || !emailInput || !questionInput || !message || !button || !buttonText) {
			return;
		}

		function clearTimers() {
			if (loadingTimer) {
				clearTimeout(loadingTimer);
				loadingTimer = null;
			}

			if (resetTimer) {
				clearTimeout(resetTimer);
				resetTimer = null;
			}
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
			inputs.forEach(function (input) {
				var control = getControl(input);

				input.classList.remove('is-invalid', 'is-success');

				if (control) {
					control.classList.remove('is-invalid', 'is-success');
				}
			});
		}

		function resetButtonState() {
			form.classList.remove('is-success');
			button.classList.remove('is-success', 'is-loading');
			button.disabled = false;
			buttonText.textContent = defaultButtonText;

			if (buttonIcon) {
				buttonIcon.style.display = '';
			}
		}

		function resetFormState() {
			clearTimers();
			form.reset();
			clearValidationState();
			clearMessage();
			resetButtonState();
		}

		function showError(text, input) {
			clearTimers();
			clearValidationState();

			inputs.forEach(function (field) {
				var control = getControl(field);
				var isTarget = field === input;

				field.classList.toggle('is-invalid', isTarget);
				field.classList.remove('is-success');

				if (control) {
					control.classList.toggle('is-invalid', isTarget);
					control.classList.remove('is-success');
				}
			});

			setMessage(text, 'is-error');
			resetButtonState();
		}

		function showLoadingState() {
			button.disabled = true;
			button.classList.add('is-loading');
			button.classList.remove('is-success');
			form.classList.remove('is-success');
			buttonText.textContent = 'Обробка...';

			if (buttonIcon) {
				buttonIcon.style.display = 'none';
			}
		}

		function showSuccessState() {
			form.reset();
			clearValidationState();
			clearMessage();

			form.classList.add('is-success');
			button.classList.remove('is-loading');
			button.classList.add('is-success');
			button.disabled = true;
			buttonText.textContent = 'Запит відправлено ✓';

			if (buttonIcon) {
				buttonIcon.style.display = 'none';
			}

			resetTimer = setTimeout(resetFormState, SUCCESS_RESET_DELAY);
		}

		emailInput.addEventListener('input', function () {
			var sanitized = sanitizeEmailInput(emailInput.value);

			if (sanitized !== emailInput.value) {
				emailInput.value = sanitized;
			}

			clearValidationState();
			clearMessage();
		});

		inputs.forEach(function (input) {
			input.addEventListener('input', function () {
				if (input === emailInput) {
					return;
				}

				clearValidationState();
				clearMessage();
			});
		});

		form.addEventListener('submit', function (event) {
			event.preventDefault();

			var nameValue = nameInput.value.trim();
			var emailValue = sanitizeEmailInput(emailInput.value);
			var questionValue = questionInput.value.trim();

			if (emailValue !== emailInput.value) {
				emailInput.value = emailValue;
			}

			if (!nameValue) {
				showError(MESSAGES.nameEmpty, nameInput);
				return;
			}

			if (!emailValue) {
				showError(MESSAGES.emailEmpty, emailInput);
				return;
			}

			if (!isValidEmail(emailValue)) {
				showError(MESSAGES.emailInvalid, emailInput);
				return;
			}

			if (!questionValue) {
				showError(MESSAGES.questionEmpty, questionInput);
				return;
			}

			if (questionValue.length <= 10) {
				showError(MESSAGES.questionTooShort, questionInput);
				return;
			}

			clearTimers();
			showLoadingState();

			loadingTimer = setTimeout(function () {
				loadingTimer = null;
				showSuccessState();
			}, LOADING_DELAY);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initContactsForm);
	} else {
		initContactsForm();
	}
})();
