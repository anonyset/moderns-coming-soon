jQuery(function ($) {
jQuery(function ($) {
	var restBase = (mcsFrontend.restUrl || '').replace(/\/$/, '');

	function handleSubscribe(form) {
		var $form = $(form);
		var data = {
			email: $form.find('input[name="email"]').val(),
			hp: $form.find('input[name="hp"]').val(),
		};

		$form.find('.mcs-message').text('');

		$.ajax({
			url: restBase + '/subscribe',
			method: 'POST',
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce', mcsFrontend.nonce);
			},
			data: data,
		})
			.done(function () {
				$form.find('.mcs-message').text(mcsFrontend.successText || 'Saved.');
				$form.trigger('reset');
			})
			.fail(function (xhr) {
				var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Error';
				$form.find('.mcs-message').text(msg);
			});
	}

	$(document).on('submit', '.mcs-subscribe-form', function (e) {
		e.preventDefault();
		handleSubscribe(this);
	});

	function initCountdown() {
		$('.mcs-countdown, .mcs-countdown-block').each(function () {
			var $el = $(this);
			var dateStr = $el.data('date');
			if (!dateStr) {
				return;
			}
			var target = new Date(dateStr).getTime();
			if (!target) {
				return;
			}

			function tick() {
				var now = new Date().getTime();
				var dist = target - now;
				if (dist < 0) {
					$el.find('[data-part]').text('00');
					return;
				}

				var days = Math.floor(dist / (1000 * 60 * 60 * 24));
				var hours = Math.floor((dist / (1000 * 60 * 60)) % 24);
				var minutes = Math.floor((dist / (1000 * 60)) % 60);
				var seconds = Math.floor((dist / 1000) % 60);

				$el.find('[data-part="days"]').text(String(days).padStart(2, '0'));
				$el.find('[data-part="hours"]').text(String(hours).padStart(2, '0'));
				$el.find('[data-part="minutes"]').text(String(minutes).padStart(2, '0'));
				$el.find('[data-part="seconds"]').text(String(seconds).padStart(2, '0'));
				$el.find('.mcs-countdown-values').text(days + 'd ' + hours + 'h ' + minutes + 'm ' + seconds + 's');
			}

			tick();
			setInterval(tick, 1000);
		});
	}

	initCountdown();
});
