<?php
/**
 * FrontendForm — Renders inline event creation form for PaneEngine panel-forms.php
 *
 * Hooks into `apollo/events/render_create_form` fired by panel-forms.php
 * Covers ALL 17 meta keys + 5 taxonomies from apollo-registry.json
 *
 * Auto-generated fields NOT shown (system-managed):
 *   _event_is_gone, _event_view_count
 *
 * @package Apollo\Event
 */

declare(strict_types=1);

namespace Apollo\Event;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class FrontendForm {

	public function __construct() {
		add_action( 'apollo/events/render_create_form', array( $this, 'render' ), 10, 1 );
	}

	/**
	 * Render the inline event creation form inside panel-forms.php
	 *
	 * @param int $user_id Current user ID
	 */
	public function render( int $user_id ): void {
		if ( ! is_user_logged_in() ) {
			echo '<p>' . esc_html__( 'Faça login para criar eventos.', 'apollo-events' ) . '</p>';
			return;
		}

		// Fetch data for selects
		$locals = get_posts(
			array(
				'post_type'      => 'loc',
				'post_status'    => 'publish',
				'posts_per_page' => 500,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		// Fallback: try "local" if "loc" returns empty
		if ( empty( $locals ) ) {
			$locals = get_posts(
				array(
					'post_type'      => 'local',
					'post_status'    => 'publish',
					'posts_per_page' => 500,
					'orderby'        => 'title',
					'order'          => 'ASC',
				)
			);
		}

		$djs = get_posts(
			array(
				'post_type'      => 'dj',
				'post_status'    => 'publish',
				'posts_per_page' => 500,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);

		$taxonomies = array(
			'event_category' => $this->get_taxonomy_terms( 'event_category' ),
			'event_type'     => $this->get_taxonomy_terms( 'event_type' ),
			'event_tag'      => $this->get_taxonomy_terms( 'event_tag' ),
			'sound'          => $this->get_taxonomy_terms( 'sound' ),
			'season'         => $this->get_taxonomy_terms( 'season' ),
		);

		$rest_url = esc_url_raw( rest_url( 'apollo/v1/events' ) );
		$nonce    = wp_create_nonce( 'wp_rest' );
		?>
		<div class="apl-form apl-form--event" id="apolloEventForm">
			<div class="apl-form__header">
				<i class="ri-calendar-event-fill"></i>
				<h3><?php esc_html_e( 'Criar Evento', 'apollo-events' ); ?></h3>
			</div>

			<form id="apl-event-create" autocomplete="off" novalidate>

				<!-- ═══ BÁSICO ═══ -->
				<fieldset class="apl-form__fieldset">
					<legend><i class="ri-information-line"></i> <?php esc_html_e( 'Informações Básicas', 'apollo-events' ); ?></legend>

					<div class="apl-form__field">
						<label for="ef_title"><?php esc_html_e( 'Título do Evento', 'apollo-events' ); ?> <span class="req">*</span></label>
						<input type="text" id="ef_title" name="title" required maxlength="200" placeholder="<?php esc_attr_e( 'Nome do evento', 'apollo-events' ); ?>">
					</div>

					<div class="apl-form__field">
						<label for="ef_content"><?php esc_html_e( 'Descrição', 'apollo-events' ); ?></label>
						<textarea id="ef_content" name="content" rows="4" placeholder="<?php esc_attr_e( 'Descreva o evento...', 'apollo-events' ); ?>"></textarea>
					</div>
				</fieldset>

				<!-- ═══ DATA / HORA ═══ -->
				<fieldset class="apl-form__fieldset">
					<legend><i class="ri-time-line"></i> <?php esc_html_e( 'Data e Horário', 'apollo-events' ); ?></legend>

					<div class="apl-form__row">
						<div class="apl-form__field apl-form__field--half">
							<label for="ef_start_date"><?php esc_html_e( 'Data Início', 'apollo-events' ); ?> <span class="req">*</span></label>
							<input type="date" id="ef_start_date" name="start_date" required>
						</div>
						<div class="apl-form__field apl-form__field--half">
							<label for="ef_end_date"><?php esc_html_e( 'Data Fim', 'apollo-events' ); ?></label>
							<input type="date" id="ef_end_date" name="end_date">
						</div>
					</div>

					<div class="apl-form__row">
						<div class="apl-form__field apl-form__field--half">
							<label for="ef_start_time"><?php esc_html_e( 'Hora Início', 'apollo-events' ); ?></label>
							<input type="time" id="ef_start_time" name="start_time">
						</div>
						<div class="apl-form__field apl-form__field--half">
							<label for="ef_end_time"><?php esc_html_e( 'Hora Fim', 'apollo-events' ); ?></label>
							<input type="time" id="ef_end_time" name="end_time">
						</div>
					</div>
				</fieldset>

				<!-- ═══ LOCAL ═══ -->
				<fieldset class="apl-form__fieldset">
					<legend><i class="ri-map-pin-line"></i> <?php esc_html_e( 'Local', 'apollo-events' ); ?></legend>

					<div class="apl-form__field">
						<label for="ef_loc_id"><?php esc_html_e( 'Local do Evento', 'apollo-events' ); ?></label>
						<select id="ef_loc_id" name="loc_id">
							<option value=""><?php esc_html_e( 'Selecione um local...', 'apollo-events' ); ?></option>
							<?php foreach ( $locals as $loc ) : ?>
								<option value="<?php echo esc_attr( (string) $loc->ID ); ?>"><?php echo esc_html( $loc->post_title ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</fieldset>

				<!-- ═══ LINEUP ═══ -->
				<fieldset class="apl-form__fieldset">
					<legend><i class="ri-disc-line"></i> <?php esc_html_e( 'Lineup', 'apollo-events' ); ?></legend>

					<div class="apl-form__field">
						<label for="ef_dj_ids"><?php esc_html_e( 'DJs', 'apollo-events' ); ?></label>
						<select id="ef_dj_ids" name="dj_ids" multiple size="5">
							<?php foreach ( $djs as $dj ) : ?>
								<option value="<?php echo esc_attr( (string) $dj->ID ); ?>"><?php echo esc_html( $dj->post_title ); ?></option>
							<?php endforeach; ?>
						</select>
						<span class="apl-form__hint"><?php esc_html_e( 'Segure Ctrl/Cmd para selecionar múltiplos', 'apollo-events' ); ?></span>
					</div>

					<div class="apl-form__field">
						<label for="ef_dj_slots"><?php esc_html_e( 'DJ Slots — JSON', 'apollo-events' ); ?></label>
						<textarea id="ef_dj_slots" name="dj_slots" rows="3" placeholder='[{"dj_id":0,"start_time":"23:00","end_time":"01:00","label":"Main Stage"}]' style="font-family:monospace;font-size:12px;"></textarea>
						<span class="apl-form__hint"><?php esc_html_e( 'Opcional. Array JSON: {dj_id, start_time, end_time, label}', 'apollo-events' ); ?></span>
					</div>
				</fieldset>

				<!-- ═══ INGRESSOS ═══ -->
				<fieldset class="apl-form__fieldset">
					<legend><i class="ri-ticket-line"></i> <?php esc_html_e( 'Ingressos', 'apollo-events' ); ?></legend>

					<div class="apl-form__row">
						<div class="apl-form__field apl-form__field--half">
							<label for="ef_ticket_url"><?php esc_html_e( 'URL dos Ingressos', 'apollo-events' ); ?></label>
							<input type="url" id="ef_ticket_url" name="ticket_url" placeholder="https://...">
						</div>
						<div class="apl-form__field apl-form__field--half">
							<label for="ef_ticket_price"><?php esc_html_e( 'Preço', 'apollo-events' ); ?></label>
							<input type="text" id="ef_ticket_price" name="ticket_price" placeholder="R$ 50,00">
						</div>
					</div>

					<div class="apl-form__row">
						<div class="apl-form__field apl-form__field--half">
							<label for="ef_coupon_code"><?php esc_html_e( 'Código de Cupom', 'apollo-events' ); ?></label>
							<input type="text" id="ef_coupon_code" name="coupon_code" placeholder="APOLLO20">
						</div>
						<div class="apl-form__field apl-form__field--half">
							<label for="ef_list_url"><?php esc_html_e( 'URL Lista Amiga', 'apollo-events' ); ?></label>
							<input type="url" id="ef_list_url" name="list_url" placeholder="https://...">
						</div>
					</div>
				</fieldset>

				<!-- ═══ MÍDIA ═══ -->
				<fieldset class="apl-form__fieldset">
					<legend><i class="ri-image-line"></i> <?php esc_html_e( 'Mídia', 'apollo-events' ); ?></legend>

					<div class="apl-form__field">
						<label for="ef_banner"><?php esc_html_e( 'Banner (ID da imagem)', 'apollo-events' ); ?></label>
						<input type="number" id="ef_banner" name="banner" min="0" placeholder="0">
						<span class="apl-form__hint"><?php esc_html_e( 'ID do anexo da biblioteca de mídia', 'apollo-events' ); ?></span>
					</div>

					<div class="apl-form__field">
						<label for="ef_video_url"><?php esc_html_e( 'Vídeo Promocional (URL)', 'apollo-events' ); ?></label>
						<input type="url" id="ef_video_url" name="video_url" placeholder="https://youtube.com/watch?v=...">
					</div>

					<div class="apl-form__field">
						<label for="ef_gallery"><?php esc_html_e( 'Galeria (IDs separados por vírgula, máx. 3)', 'apollo-events' ); ?></label>
						<input type="text" id="ef_gallery" name="gallery" placeholder="100,101,102">
					</div>
				</fieldset>

				<!-- ═══ TAXONOMIAS ═══ -->
				<fieldset class="apl-form__fieldset">
					<legend><i class="ri-price-tag-3-line"></i> <?php esc_html_e( 'Classificação', 'apollo-events' ); ?></legend>

					<div class="apl-form__row">
						<div class="apl-form__field apl-form__field--half">
							<label for="ef_categories"><?php esc_html_e( 'Categorias', 'apollo-events' ); ?></label>
							<select id="ef_categories" name="categories" multiple size="4">
								<?php foreach ( $taxonomies['event_category'] as $term ) : ?>
									<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="apl-form__field apl-form__field--half">
							<label for="ef_types"><?php esc_html_e( 'Tipos', 'apollo-events' ); ?></label>
							<select id="ef_types" name="types" multiple size="4">
								<?php foreach ( $taxonomies['event_type'] as $term ) : ?>
									<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<div class="apl-form__row">
						<div class="apl-form__field apl-form__field--half">
							<label for="ef_tags"><?php esc_html_e( 'Tags', 'apollo-events' ); ?></label>
							<select id="ef_tags" name="tags" multiple size="4">
								<?php foreach ( $taxonomies['event_tag'] as $term ) : ?>
									<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="apl-form__field apl-form__field--half">
							<label for="ef_sounds"><?php esc_html_e( 'Gêneros Musicais', 'apollo-events' ); ?></label>
							<select id="ef_sounds" name="sounds" multiple size="4">
								<?php foreach ( $taxonomies['sound'] as $term ) : ?>
									<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<div class="apl-form__field">
						<label for="ef_seasons"><?php esc_html_e( 'Temporadas', 'apollo-events' ); ?></label>
						<select id="ef_seasons" name="seasons" multiple size="3">
							<?php foreach ( $taxonomies['season'] as $term ) : ?>
								<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</fieldset>

				<!-- ═══ CONFIGURAÇÕES ═══ -->
				<fieldset class="apl-form__fieldset">
					<legend><i class="ri-settings-3-line"></i> <?php esc_html_e( 'Configurações', 'apollo-events' ); ?></legend>

					<div class="apl-form__row">
						<div class="apl-form__field apl-form__field--half">
							<label for="ef_privacy"><?php esc_html_e( 'Privacidade', 'apollo-events' ); ?></label>
							<select id="ef_privacy" name="privacy">
								<option value="public"><?php esc_html_e( 'Público', 'apollo-events' ); ?></option>
								<option value="private"><?php esc_html_e( 'Privado', 'apollo-events' ); ?></option>
								<option value="invite"><?php esc_html_e( 'Apenas Convidados', 'apollo-events' ); ?></option>
							</select>
						</div>
						<div class="apl-form__field apl-form__field--half">
							<label for="ef_event_status"><?php esc_html_e( 'Status', 'apollo-events' ); ?></label>
							<select id="ef_event_status" name="event_status">
								<option value="scheduled"><?php esc_html_e( 'Agendado', 'apollo-events' ); ?></option>
								<option value="ongoing"><?php esc_html_e( 'Em andamento', 'apollo-events' ); ?></option>
								<option value="postponed"><?php esc_html_e( 'Adiado', 'apollo-events' ); ?></option>
								<option value="cancelled"><?php esc_html_e( 'Cancelado', 'apollo-events' ); ?></option>
								<option value="finished"><?php esc_html_e( 'Finalizado', 'apollo-events' ); ?></option>
							</select>
						</div>
					</div>
				</fieldset>

				<!-- ═══ SUBMIT ═══ -->
				<div class="apl-form__error" id="efError" style="display:none;"></div>

				<button type="submit" class="apl-form__submit" id="efSubmit">
					<i class="ri-send-plane-fill"></i>
					<span><?php esc_html_e( 'Criar Evento', 'apollo-events' ); ?></span>
				</button>

			</form>
		</div>

		<style>
		.apl-form--event { max-width: 640px; }
		.apl-form__header { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; }
		.apl-form__header i { font-size: 22px; color: var(--primary, #f45f00); }
		.apl-form__header h3 { margin: 0; font-size: 18px; font-weight: 700; color: var(--txt, #fff); }
		.apl-form__fieldset {
			border: 1px solid var(--border, rgba(255,255,255,.08));
			border-radius: 12px;
			padding: 16px;
			margin: 0 0 16px;
		}
		.apl-form__fieldset legend {
			display: flex; align-items: center; gap: 6px;
			font-size: 14px; font-weight: 600; color: var(--txt-muted, #aaa);
			padding: 0 8px;
		}
		.apl-form__fieldset legend i { font-size: 16px; }
		.apl-form__field { margin-bottom: 12px; }
		.apl-form__field label {
			display: block; font-size: 13px; font-weight: 600;
			color: var(--txt, #fff); margin-bottom: 4px;
		}
		.apl-form__field .req { color: #ef4444; }
		.apl-form__field input,
		.apl-form__field select,
		.apl-form__field textarea {
			width: 100%; padding: 10px 12px;
			background: var(--bg-card, #1a1a2e);
			border: 1px solid var(--border, rgba(255,255,255,.08));
			border-radius: 8px; color: var(--txt, #fff);
			font-size: 14px; transition: border-color .2s;
		}
		.apl-form__field input:focus,
		.apl-form__field select:focus,
		.apl-form__field textarea:focus {
			outline: none; border-color: var(--primary, #f45f00);
		}
		.apl-form__field select[multiple] { min-height: 80px; }
		.apl-form__hint { display: block; font-size: 11px; color: var(--txt-muted, #888); margin-top: 4px; }
		.apl-form__row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
		@media (max-width: 480px) { .apl-form__row { grid-template-columns: 1fr; } }
		.apl-form__error {
			background: rgba(239,68,68,.12); color: #ef4444;
			padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 12px;
		}
		.apl-form__submit {
			display: flex; align-items: center; justify-content: center; gap: 8px;
			width: 100%; padding: 12px; border: none; border-radius: 10px;
			background: var(--primary, #f45f00); color: #fff; font-size: 15px;
			font-weight: 700; cursor: pointer; transition: opacity .2s;
		}
		.apl-form__submit:hover { opacity: .9; }
		.apl-form__submit:disabled { opacity: .5; cursor: not-allowed; }
		.apl-form__submit i { font-size: 18px; }
		</style>

		<script>
		(function(){
			'use strict';
			var REST  = '<?php echo esc_js( $rest_url ); ?>';
			var NONCE = '<?php echo esc_js( $nonce ); ?>';
			var form  = document.getElementById('apl-event-create');
			var errEl = document.getElementById('efError');
			var btn   = document.getElementById('efSubmit');

			if (!form) return;

			form.addEventListener('submit', async function(e) {
				e.preventDefault();
				errEl.style.display = 'none';
				btn.disabled = true;
				btn.querySelector('span').textContent = 'Criando...';

				try {
					var data = {};

					/* Simple text/url/date/time fields */
					['title','content','start_date','end_date','start_time','end_time',
					'ticket_url','ticket_price','coupon_code','list_url','video_url',
					'privacy','event_status'].forEach(function(k) {
						var el = form.querySelector('[name="'+k+'"]');
						if (el && el.value.trim()) data[k] = el.value.trim();
					});

					/* Number: banner, loc_id */
					['banner','loc_id'].forEach(function(k) {
						var el = form.querySelector('[name="'+k+'"]');
						if (el && el.value) data[k] = parseInt(el.value, 10) || 0;
					});

					/* Multi-select arrays: dj_ids, categories, types, tags, sounds, seasons */
					['dj_ids','categories','types','tags','sounds','seasons'].forEach(function(k) {
						var el = form.querySelector('[name="'+k+'"]');
						if (!el) return;
						var vals = Array.from(el.selectedOptions).map(function(o){ return o.value; });
						if (vals.length) {
							data[k] = k === 'dj_ids' ? vals.map(function(v){ return parseInt(v,10); }) : vals;
						}
					});

					/* Gallery: comma-separated → array of ints */
					var galEl = form.querySelector('[name="gallery"]');
					if (galEl && galEl.value.trim()) {
						data.gallery = galEl.value.split(',').map(function(v){ return parseInt(v.trim(),10); }).filter(function(n){ return n > 0; });
					}

					/* DJ Slots: JSON string → array */
					var slotsEl = form.querySelector('[name="dj_slots"]');
					if (slotsEl && slotsEl.value.trim()) {
						try { data.dj_slots = JSON.parse(slotsEl.value.trim()); } catch(ex) { /* ignore */ }
					}

					if (!data.title) throw new Error('Título é obrigatório.');
					if (!data.start_date) throw new Error('Data de início é obrigatória.');

					var res = await fetch(REST, {
						method: 'POST',
						headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': NONCE },
						credentials: 'same-origin',
						body: JSON.stringify(data)
					});

					var result = await res.json();

					if (res.ok && (result.id || result.permalink)) {
						btn.querySelector('span').textContent = 'Evento criado!';
						btn.style.background = '#22c55e';
						if (result.permalink) {
							setTimeout(function(){ window.location.href = result.permalink; }, 1500);
						}
					} else {
						throw new Error(result.error || result.message || 'Erro ao criar evento.');
					}
				} catch(err) {
					errEl.textContent = err.message;
					errEl.style.display = '';
					btn.disabled = false;
					btn.querySelector('span').textContent = 'Criar Evento';
				}
			});
		})();
		</script>
		<?php
	}

	/**
	 * Helper: Get taxonomy terms safely
	 *
	 * @param string $taxonomy Taxonomy slug
	 * @return array Array of WP_Term objects
	 */
	private function get_taxonomy_terms( string $taxonomy ): array {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			return array();
		}

		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			)
		);

		return is_wp_error( $terms ) ? array() : $terms;
	}
}
