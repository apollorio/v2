/**
 * Apollo Users - Profile Page JS
 *
 * Handles: Rating system, Depoimentos (AJAX), Feed tabs, Toast notifications
 *
 * @package Apollo\Users
 */

(function($) {
	'use strict';

	const cfg = window.apolloProfile || {};

	// ═══════════ TOAST ═══════════
	function showToast(message, type = 'success') {
		$('.apollo-toast').remove();
		const $toast = $('<div class="apollo-toast ' + type + '">' + message + '</div>');
		$('body').append($toast);
		setTimeout(() => $toast.addClass('show'), 10);
		setTimeout(() => {
			$toast.removeClass('show');
			setTimeout(() => $toast.remove(), 300);
		}, 3000);
	}

	// ═══════════ RATING SYSTEM ═══════════
	function initRatings() {
		if (!cfg.isLoggedIn || cfg.isOwn) return;

		$('.rating-emoji[data-score]').on('click', function() {
			const $emoji = $(this);
			const $row = $emoji.closest('.rating-row');
			const category = $row.data('category');
			const score = parseInt($emoji.data('score'));
			const catInfo = cfg.categories[category];

			// Optimistic UI: fill emojis up to clicked score
			$row.find('.rating-emoji').each(function(i) {
				const $e = $(this);
				const s = parseInt($e.data('score'));
				if (s <= score) {
					$e.addClass('filled').removeClass('half-filled')
					  .css('color', catInfo.color);
				} else {
					$e.removeClass('filled half-filled').css('color', '');
				}
			});

			// AJAX submit
			$.ajax({
				url: cfg.ajaxUrl,
				method: 'POST',
				data: {
					action: 'apollo_submit_rating',
					nonce: cfg.nonce,
					target_user_id: cfg.userId,
					category: category,
					score: score
				},
				success: function(res) {
					if (res.success) {
						showToast('Avaliação registrada!', 'success');
						// Update vote count if present
						if (res.data && res.data.averages) {
							updateRatingDisplay(res.data.averages);
						}
					} else {
						showToast(res.data?.message || 'Erro', 'error');
					}
				},
				error: function() {
					showToast('Erro de conexão', 'error');
				}
			});
		});

		// Hover effect
		$('.rating-emoji[data-score]').on('mouseenter', function() {
			const $row = $(this).closest('.rating-row');
			const score = parseInt($(this).data('score'));
			const catInfo = cfg.categories[$row.data('category')];

			$row.find('.rating-emoji').each(function() {
				const s = parseInt($(this).data('score'));
				if (s <= score) {
					$(this).css('opacity', '0.7').css('color', catInfo.color);
				}
			});
		}).on('mouseleave', function() {
			const $row = $(this).closest('.rating-row');
			$row.find('.rating-emoji').each(function() {
				$(this).css('opacity', '');
			});
		});
	}

	function updateRatingDisplay(averages) {
		// Update the vote count display
		const totalVotes = Object.values(averages).reduce((sum, v) => sum + (v.count || 0), 0);
		const $note = $('.rating-note');
		if (totalVotes > 0 && $note.length) {
			$note.text(totalVotes + ' voto' + (totalVotes > 1 ? 's' : ''));
		}
	}

	// ═══════════ DEPOIMENTOS ═══════════
	function initDepoimentos() {
		// Submit form
		$('#depoimento-form').on('submit', function(e) {
			e.preventDefault();
			const $form = $(this);
			const $btn = $form.find('.depo-submit-btn');
			const content = $form.find('textarea').val().trim();

			if (content.length < 10) {
				showToast('Mínimo 10 caracteres.', 'error');
				return;
			}

			$btn.prop('disabled', true).html('<i class="ri-loader-4-line"></i> Enviando...');

			$.ajax({
				url: cfg.ajaxUrl,
				method: 'POST',
				data: {
					action: 'apollo_submit_depoimento',
					nonce: cfg.nonce,
					target_user_id: cfg.userId,
					content: content
				},
				success: function(res) {
					if (res.success) {
						showToast('Depoimento publicado!', 'success');
						$form.hide();

						// Prepend new depoimento to list
						const d = res.data.depoimento;
						const html = buildDepoimentoHTML(d);
						$('.depo-empty').remove();
						$('#depoimentos-list').prepend(html);

						// Update count
						const $count = $('.depo-count');
						$count.text(parseInt($count.text()) + 1);
					} else {
						showToast(res.data?.message || 'Erro', 'error');
						$btn.prop('disabled', false).html('<i class="ri-send-plane-fill"></i> Publicar');
					}
				},
				error: function() {
					showToast('Erro de conexão', 'error');
					$btn.prop('disabled', false).html('<i class="ri-send-plane-fill"></i> Publicar');
				}
			});
		});

		// Delete depoimento
		$(document).on('click', '.depo-delete-btn', function() {
			if (!confirm('Remover este depoimento?')) return;

			const $btn = $(this);
			const id = $btn.data('id');
			const $item = $btn.closest('.depo-item');

			$.ajax({
				url: cfg.ajaxUrl,
				method: 'POST',
				data: {
					action: 'apollo_delete_depoimento',
					nonce: cfg.nonce,
					comment_id: id
				},
				success: function(res) {
					if (res.success) {
						$item.fadeOut(300, function() { $(this).remove(); });
						showToast('Depoimento removido.', 'success');
						const $count = $('.depo-count');
						$count.text(Math.max(0, parseInt($count.text()) - 1));
					} else {
						showToast(res.data?.message || 'Erro', 'error');
					}
				}
			});
		});
	}

	function buildDepoimentoHTML(d) {
		const deleteBtn = d.can_delete
			? '<button class="depo-delete-btn" data-id="' + d.id + '" title="Excluir"><i class="ri-close-line"></i></button>'
			: '';
		return '<div class="depo-item" data-id="' + d.id + '">' +
			deleteBtn +
			'<i class="ri-double-quotes-l depo-quote-icon"></i>' +
			'<blockquote class="depo-content">' + d.content + '</blockquote>' +
			'<div class="depo-author">' +
				'<a href="/id/' + d.author_login + '"><img src="' + d.avatar_url + '" class="depo-author-avatar" alt=""></a>' +
				'<div>' +
					'<a href="/id/' + d.author_login + '" class="depo-author-name">' + d.author_name + '</a>' +
					'<div class="depo-author-role">' + (d.membership || '') + '</div>' +
				'</div>' +
				'<span class="depo-date">' + d.date_human + '</span>' +
			'</div>' +
		'</div>';
	}

	// ═══════════ FEED TABS ═══════════
	function initFeedTabs() {
		$('.feed-tab').on('click', function() {
			$('.feed-tab').removeClass('active');
			$(this).addClass('active');
			// Filter logic would go here for actual CPT filtering
		});
	}

	// ═══════════ INIT ═══════════
	$(document).ready(function() {
		initRatings();
		initDepoimentos();
		initFeedTabs();
	});

})(jQuery);
