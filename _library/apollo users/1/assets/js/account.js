/**
 * Apollo Users - Account Page JS
 *
 * Handles: Account update, Password change, Privacy settings, Account deletion
 * Ported from UsersWP account patterns.
 *
 * @package Apollo\Users
 */

(function($) {
	'use strict';

	const cfg = window.apolloAccount || {};

	function showToast(msg, type = 'success') {
		$('.apollo-toast').remove();
		const $t = $('<div class="apollo-toast ' + type + '">' + msg + '</div>');
		$('body').append($t);
		setTimeout(() => $t.addClass('show'), 10);
		setTimeout(() => { $t.removeClass('show'); setTimeout(() => $t.remove(), 300); }, 3000);
	}

	function ajaxSubmit(formId, action) {
		const $form = $(formId);
		$form.on('submit', function(e) {
			e.preventDefault();
			const $btn = $form.find('button[type="submit"]');
			const origHtml = $btn.html();
			$btn.prop('disabled', true).html('<i class="ri-loader-4-line"></i> Salvando...');

			$.ajax({
				url: cfg.ajaxUrl,
				method: 'POST',
				data: $form.serialize(),
				success: function(res) {
					if (res.success) {
						showToast(res.data.message, 'success');
						if (res.data.redirect) {
							setTimeout(() => window.location.href = res.data.redirect, 1000);
						}
					} else {
						showToast(res.data?.message || 'Erro ao salvar.', 'error');
					}
					$btn.prop('disabled', false).html(origHtml);
				},
				error: function() {
					showToast('Erro de conexão.', 'error');
					$btn.prop('disabled', false).html(origHtml);
				}
			});
		});
	}

	// Bio character counter
	function initBioCounter() {
		$('#bio').on('input', function() {
			$('#bio-count').text($(this).val().length);
		});
	}

	// Password strength indicator
	function initPasswordStrength() {
		$('#new_password').on('input', function() {
			const pass = $(this).val();
			const $el = $('#password-strength');
			if (pass.length < 8) {
				$el.html('<span class="strength-weak">Fraca</span>');
			} else if (pass.length < 12 || !/[A-Z]/.test(pass) || !/[0-9]/.test(pass)) {
				$el.html('<span class="strength-medium">Média</span>');
			} else {
				$el.html('<span class="strength-strong">Forte</span>');
			}
		});
	}

	// Delete account confirmation
	function initDeleteConfirm() {
		$('#delete_confirm').on('change', function() {
			$('#delete-btn').prop('disabled', !this.checked);
		});
	}

	$(document).ready(function() {
		ajaxSubmit('#account-form', 'apollo_update_account');
		ajaxSubmit('#password-form', 'apollo_change_password');
		ajaxSubmit('#privacy-form', 'apollo_update_privacy');
		ajaxSubmit('#delete-form', 'apollo_delete_account');
		initBioCounter();
		initPasswordStrength();
		initDeleteConfirm();
	});

})(jQuery);
