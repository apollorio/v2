<?php
/**
 * Template: Create Event Form — Base Style
 *
 * @package Apollo\Event
 */

if ( ! defined( 'ABSPATH' ) ) exit;

wp_enqueue_style( 'apollo-events' );
wp_enqueue_script( 'apollo-events' );
?>

<div class="a-eve-create-form">
	<h2><?php esc_html_e( 'Criar Evento', 'apollo-events' ); ?></h2>

	<form id="a-eve-create-form" class="a-eve-form" method="post">
		<?php wp_nonce_field( 'apollo_event_create', 'apollo_event_nonce' ); ?>

		<div class="a-eve-form__group">
			<label for="event_title"><?php esc_html_e( 'Título do Evento', 'apollo-events' ); ?> *</label>
			<input type="text" id="event_title" name="title" required class="a-eve-form__input">
		</div>

		<div class="a-eve-form__group">
			<label for="event_content"><?php esc_html_e( 'Descrição', 'apollo-events' ); ?></label>
			<?php wp_editor( '', 'event_content', [
				'textarea_name' => 'content',
				'media_buttons' => true,
				'textarea_rows' => 8,
				'teeny'         => false,
			] ); ?>
		</div>

		<div class="a-eve-form__row">
			<div class="a-eve-form__group a-eve-form__group--half">
				<label for="event_start_date"><?php esc_html_e( 'Data Início', 'apollo-events' ); ?> *</label>
				<input type="date" id="event_start_date" name="start_date" required class="a-eve-form__input">
			</div>
			<div class="a-eve-form__group a-eve-form__group--half">
				<label for="event_end_date"><?php esc_html_e( 'Data Fim', 'apollo-events' ); ?></label>
				<input type="date" id="event_end_date" name="end_date" class="a-eve-form__input">
			</div>
		</div>

		<div class="a-eve-form__row">
			<div class="a-eve-form__group a-eve-form__group--half">
				<label for="event_start_time"><?php esc_html_e( 'Hora Início', 'apollo-events' ); ?></label>
				<input type="time" id="event_start_time" name="start_time" class="a-eve-form__input">
			</div>
			<div class="a-eve-form__group a-eve-form__group--half">
				<label for="event_end_time"><?php esc_html_e( 'Hora Fim', 'apollo-events' ); ?></label>
				<input type="time" id="event_end_time" name="end_time" class="a-eve-form__input">
			</div>
		</div>

		<div class="a-eve-form__group">
			<label for="event_banner"><?php esc_html_e( 'Banner', 'apollo-events' ); ?></label>
			<input type="hidden" id="event_banner" name="banner" value="">
			<div id="event_banner_preview" class="a-eve-form__preview"></div>
			<button type="button" id="event_banner_btn" class="a-eve-form__upload-btn">
				<?php esc_html_e( 'Selecionar Imagem', 'apollo-events' ); ?>
			</button>
		</div>

		<div class="a-eve-form__group">
			<label for="event_ticket_url"><?php esc_html_e( 'URL dos Ingressos', 'apollo-events' ); ?></label>
			<input type="url" id="event_ticket_url" name="ticket_url" class="a-eve-form__input" placeholder="https://...">
		</div>

		<div class="a-eve-form__group">
			<label for="event_ticket_price"><?php esc_html_e( 'Preço', 'apollo-events' ); ?></label>
			<input type="text" id="event_ticket_price" name="ticket_price" class="a-eve-form__input" placeholder="R$ 50,00">
		</div>

		<div class="a-eve-form__group">
			<label for="event_privacy"><?php esc_html_e( 'Privacidade', 'apollo-events' ); ?></label>
			<select id="event_privacy" name="privacy" class="a-eve-form__input">
				<option value="public"><?php esc_html_e( 'Público', 'apollo-events' ); ?></option>
				<option value="private"><?php esc_html_e( 'Privado', 'apollo-events' ); ?></option>
				<option value="invite"><?php esc_html_e( 'Apenas Convidados', 'apollo-events' ); ?></option>
			</select>
		</div>

		<div class="a-eve-form__actions">
			<button type="submit" class="a-eve-form__submit">
				<?php esc_html_e( 'Criar Evento', 'apollo-events' ); ?>
			</button>
		</div>

		<div id="a-eve-form-message" class="a-eve-form__message" style="display:none;"></div>
	</form>
</div>
