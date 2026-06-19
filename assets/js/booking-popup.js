(function () {
	'use strict';

	var LOADING_DELAY = 800;
	var SUCCESS_RESET_DELAY = 3000;
	var CLOSE_ANIMATION_MS = 350;

	var MESSAGES = {
		nameEmpty: "Будь ласка, введіть ваше ім'я.",
		peopleEmpty: 'Будь ласка, вкажіть кількість людей.',
		ageEmpty: 'Будь ласка, вкажіть вік учасників.',
		dateEmpty: 'Будь ласка, оберіть дату.'
	};

	function initBookingPopup() {
		var popup = document.getElementById('booking-popup');

		if (!popup) {
			return;
		}

		var dialog = popup.querySelector('.booking-popup__dialog');
		var form = popup.querySelector('.booking-popup-form');
		var nameInput = popup.querySelector('#booking-popup-name');
		var peopleInput = popup.querySelector('#booking-popup-people');
		var ageInput = popup.querySelector('#booking-popup-age');
		var dateInput = popup.querySelector('#booking-popup-date');
		var message = popup.querySelector('.booking-popup-form__message');
		var button = popup.querySelector('#booking-popup-submit');
		var buttonText = popup.querySelector('.booking-popup-form__btn-text');
		var buttonIcon = button ? button.querySelector('.btn__icon') : null;
		var successPanel = popup.querySelector('.booking-popup__success');
		var defaultButtonText = buttonText ? buttonText.textContent : 'Створити мій день на узбережжі';
		var loadingTimer = null;
		var resetTimer = null;
		var closeTimer = null;
		var inputs = [nameInput, peopleInput, ageInput, dateInput];

		if (!dialog || !form || !nameInput || !peopleInput || !ageInput || !dateInput || !message || !button || !buttonText || !successPanel) {
			return;
		}

		function getControl(input) {
			return input ? input.closest('.booking-popup-form__control') : null;
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

			if (closeTimer) {
				clearTimeout(closeTimer);
				closeTimer = null;
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

		function resetSuccessPanel() {
			dialog.classList.remove('is-success', 'is-success-visible');
			successPanel.setAttribute('aria-hidden', 'true');
		}

		function resetFormState() {
			clearTimers();
			form.reset();
			clearValidationState();
			clearMessage();
			resetButtonState();
			resetSuccessPanel();
		}

		function showError(text, input) {
			clearTimers();
			clearValidationState();
			resetSuccessPanel();

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
			resetSuccessPanel();
			button.disabled = true;
			button.classList.add('is-loading');
			button.classList.remove('is-success');
			form.classList.remove('is-success');
			buttonText.textContent = 'Зачекайте...';

			if (buttonIcon) {
				buttonIcon.style.display = 'none';
			}
		}

		function showSuccessState() {
			form.reset();
			clearValidationState();
			clearMessage();
			resetButtonState();

			dialog.classList.add('is-success');
			successPanel.setAttribute('aria-hidden', 'false');

			requestAnimationFrame(function () {
				requestAnimationFrame(function () {
					dialog.classList.add('is-success-visible');
				});
			});

			resetTimer = setTimeout(resetFormState, SUCCESS_RESET_DELAY);
		}

		function openPopup() {
			clearTimers();
			resetFormState();
			popup.style.display = 'flex';
			popup.setAttribute('aria-hidden', 'false');
			document.body.style.overflow = 'hidden';

			requestAnimationFrame(function () {
				requestAnimationFrame(function () {
					popup.classList.add('is-open', 'is-active');
				});
			});

			window.setTimeout(function () {
				nameInput.focus();
			}, CLOSE_ANIMATION_MS);
		}

		function closePopup() {
			popup.classList.remove('is-open', 'is-active');
			popup.setAttribute('aria-hidden', 'true');
			document.body.style.overflow = '';

			closeTimer = window.setTimeout(function () {
				closeTimer = null;

				if (!popup.classList.contains('is-open')) {
					popup.style.display = 'none';
					resetFormState();
				}
			}, CLOSE_ANIMATION_MS);
		}

		document.addEventListener('click', function (event) {
			var openTrigger = event.target.closest('[data-booking-open]');

			if (openTrigger) {
				event.preventDefault();
				openPopup();
				return;
			}

			var closeTrigger = event.target.closest('[data-booking-close]');

			if (closeTrigger && popup.classList.contains('is-open')) {
				event.preventDefault();
				closePopup();
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && popup.classList.contains('is-open')) {
				closePopup();
			}
		});

		inputs.forEach(function (input) {
			var handleClear = function () {
				var control = getControl(input);
				input.classList.remove('is-invalid');
				if (control) {
					control.classList.remove('is-invalid');
				}

				// If ALL fields are now valid, clear the error message
				var allValid = inputs.every(function (field) {
					var val = field.value.trim();
					return val && (field.type !== 'number' || Number(val) >= 1);
				});

				if (allValid) {
					clearMessage();
				}
			};

			input.addEventListener('input', handleClear);
			input.addEventListener('change', handleClear);
		});

		form.addEventListener('submit', function (event) {
			event.preventDefault();

			var hasError = false;

			inputs.forEach(function (input) {
				var val = input.value.trim();
				var control = getControl(input);

				if (!val || (input.type === 'number' && Number(val) < 1)) {
					hasError = true;
					input.classList.add('is-invalid');
					if (control) {
						control.classList.add('is-invalid');
					}
				} else {
					input.classList.remove('is-invalid');
					if (control) {
						control.classList.remove('is-invalid');
					}
				}
			});

			if (hasError) {
				setMessage('Будь ласка, заповніть усі обов\'язкові поля.', 'is-error');
				return;
			}

			clearMessage();
			clearValidationState();
			clearTimers();
			showLoadingState();

			loadingTimer = window.setTimeout(function () {
				loadingTimer = null;
				showSuccessState();
			}, LOADING_DELAY);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initBookingPopup);
	} else {
		initBookingPopup();
	}
}());
