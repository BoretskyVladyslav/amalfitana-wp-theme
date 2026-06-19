(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		var dateInput = document.getElementById('tour-checkout-date');
		var form = document.querySelector('.tour-checkout-form');

		// Initialize Flatpickr on the date field
		if (dateInput && typeof flatpickr !== 'undefined') {
			flatpickr(dateInput, {
				minDate: 'today',
				dateFormat: 'Y-m-d',
				disableMobile: true
			});
		}

		// Handle form submission
		if (form) {
			form.addEventListener('submit', function (e) {
				e.preventDefault();

				var submitBtn = form.querySelector('.tour-checkout-form__submit');
				var btnText = submitBtn ? submitBtn.querySelector('.subscribe-section__btn-text') : null;
				var originalText = btnText ? btnText.textContent : '';

				var guests = document.getElementById('tour-checkout-guests');
				var tourId = form.getAttribute('data-tour-id');
				var nameInput = form.querySelector('#tour-checkout-name');
				var emailInput = form.querySelector('#tour-checkout-email');

				// Disable button and show loading text
				if (submitBtn) {
					submitBtn.disabled = true;
				}
				if (btnText) {
					btnText.textContent = 'Зачекайте...';
				}

				var params = new URLSearchParams();
				params.append('action', 'amalfitana_book_tour');
				params.append('nonce', typeof tourBookingData !== 'undefined' ? tourBookingData.nonce : '');
				params.append('tour_id', tourId || '');
				params.append('guests', guests ? guests.value : '1');
				params.append('date', dateInput ? dateInput.value : '');
				params.append('name', nameInput ? nameInput.value : '');
				params.append('email', emailInput ? emailInput.value : '');

				fetch(typeof tourBookingData !== 'undefined' ? tourBookingData.ajaxurl : '/wp-admin/admin-ajax.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
					},
					body: params.toString()
				})
				.then(function (response) {
					return response.json();
				})
				.then(function (data) {
					if (data.success && data.data && data.data.redirect_url) {
						window.location.href = data.data.redirect_url;
					} else {
						var msg = (data.data && data.data.message) ? data.data.message : 'Виникла помилка. Спробуйте ще раз.';
						alert(msg);

						// Re-enable button
						if (submitBtn) {
							submitBtn.disabled = false;
						}
						if (btnText) {
							btnText.textContent = originalText;
						}
					}
				})
				.catch(function () {
					alert('Помилка мережі. Перевірте з\'єднання та спробуйте ще раз.');

					if (submitBtn) {
						submitBtn.disabled = false;
					}
					if (btnText) {
						btnText.textContent = originalText;
					}
				});
			});
		}
	});
})();
