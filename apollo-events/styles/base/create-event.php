<?php

/**
 * Template: Create Event Form — Base Style
 *
 * @package Apollo\Event
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_enqueue_style( 'apollo-events' );
wp_enqueue_script( 'apollo-events' );

$locals = get_posts(
	array(
		'post_type'      => 'loc',
		'post_status'    => 'publish',
		'posts_per_page' => 500,
		'orderby'        => 'title',
		'order'          => 'ASC',
	)
);

$djs = get_posts(
	array(
		'post_type'      => 'dj',
		'post_status'    => 'publish',
		'posts_per_page' => 500,
		'orderby'        => 'title',
		'order'          => 'ASC',
	)
);

$event_categories = get_terms(
	array(
		'taxonomy'   => 'event_category',
		'hide_empty' => false,
	)
);

$event_types = get_terms(
	array(
		'taxonomy'   => 'event_type',
		'hide_empty' => false,
	)
);

$event_tags = get_terms(
	array(
		'taxonomy'   => 'event_tag',
		'hide_empty' => false,
	)
);

$sounds = get_terms(
	array(
		'taxonomy'   => 'sound',
		'hide_empty' => false,
	)
);

$seasons = get_terms(
	array(
		'taxonomy'   => 'season',
		'hide_empty' => false,
	)
);
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
			<?php
			wp_editor(
				'',
				'event_content',
				array(
					'textarea_name' => 'content',
					'media_buttons' => true,
					'textarea_rows' => 8,
					'teeny'         => false,
				)
			);
			?>
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
			<label for="event_loc_id"><?php esc_html_e( 'Local', 'apollo-events' ); ?></label>
			<select id="event_loc_id" name="loc_id" class="a-eve-form__input">
				<option value=""><?php esc_html_e( 'Selecione um local', 'apollo-events' ); ?></option>
				<?php foreach ( $locals as $local_post ) : ?>
					<option value="<?php echo esc_attr( (string) $local_post->ID ); ?>"><?php echo esc_html( $local_post->post_title ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="a-eve-form__group">
			<label for="event_dj_ids"><?php esc_html_e( 'Lineup (DJs)', 'apollo-events' ); ?></label>
			<select id="event_dj_ids" name="dj_ids[]" class="a-eve-form__input" multiple size="6">
				<?php foreach ( $djs as $dj_post ) : ?>
					<option value="<?php echo esc_attr( (string) $dj_post->ID ); ?>"><?php echo esc_html( $dj_post->post_title ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="a-eve-form__group">
			<label for="event_dj_slots"><?php esc_html_e( 'DJ Slots (JSON)', 'apollo-events' ); ?></label>
			<textarea id="event_dj_slots" name="dj_slots" class="a-eve-form__input" rows="3" placeholder='[{"dj_id":1,"time":"23:00"},{"dj_id":2,"time":"01:00"}]'></textarea>
			<small><?php esc_html_e( 'Opcional. Formato JSON com horários de cada DJ.', 'apollo-events' ); ?></small>
		</div>

		<div class="a-eve-form__group">
			<label for="event_gallery"><?php esc_html_e( 'Galeria (IDs)', 'apollo-events' ); ?></label>
			<input type="text" id="event_gallery" name="gallery" class="a-eve-form__input" placeholder="<?php esc_attr_e( 'IDs separados por vírgula: 101,102,103', 'apollo-events' ); ?>">
			<small><?php esc_html_e( 'Opcional. IDs de imagens da Biblioteca de Mídia.', 'apollo-events' ); ?></small>
		</div>

		<div class="a-eve-form__row">
			<div class="a-eve-form__group a-eve-form__group--half">
				<label for="event_categories"><?php esc_html_e( 'Categorias', 'apollo-events' ); ?></label>
				<select id="event_categories" name="categories[]" class="a-eve-form__input" multiple size="4">
					<?php if ( ! is_wp_error( $event_categories ) ) : ?>
						<?php foreach ( $event_categories as $term ) : ?>
							<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</div>
			<div class="a-eve-form__group a-eve-form__group--half">
				<label for="event_types"><?php esc_html_e( 'Tipos', 'apollo-events' ); ?></label>
				<select id="event_types" name="types[]" class="a-eve-form__input" multiple size="4">
					<?php if ( ! is_wp_error( $event_types ) ) : ?>
						<?php foreach ( $event_types as $term ) : ?>
							<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</div>
		</div>

		<div class="a-eve-form__row">
			<div class="a-eve-form__group a-eve-form__group--half">
				<label for="event_tags"><?php esc_html_e( 'Tags', 'apollo-events' ); ?></label>
				<select id="event_tags" name="tags[]" class="a-eve-form__input" multiple size="4">
					<?php if ( ! is_wp_error( $event_tags ) ) : ?>
						<?php foreach ( $event_tags as $term ) : ?>
							<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</div>
			<div class="a-eve-form__group a-eve-form__group--half">
				<label for="event_sounds"><?php esc_html_e( 'Gêneros Musicais', 'apollo-events' ); ?></label>
				<select id="event_sounds" name="sounds[]" class="a-eve-form__input" multiple size="4">
					<?php if ( ! is_wp_error( $sounds ) ) : ?>
						<?php foreach ( $sounds as $term ) : ?>
							<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
						<?php endforeach; ?>
					<?php endif; ?>
				</select>
			</div>
		</div>

		<div class="a-eve-form__group">
			<label for="event_seasons"><?php esc_html_e( 'Temporadas', 'apollo-events' ); ?></label>
			<select id="event_seasons" name="seasons[]" class="a-eve-form__input" multiple size="3">
				<?php if ( ! is_wp_error( $seasons ) ) : ?>
					<?php foreach ( $seasons as $term ) : ?>
						<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</div>

		<div class="a-eve-form__group">
			<label for="event_video_url"><?php esc_html_e( 'Vídeo Promocional (URL)', 'apollo-events' ); ?></label>
			<input type="url" id="event_video_url" name="video_url" class="a-eve-form__input" placeholder="https://youtube.com/watch?v=...">
		</div>

		<div class="a-eve-form__row">
			<div class="a-eve-form__group a-eve-form__group--half">
				<label for="event_coupon_code"><?php esc_html_e( 'Código de Cupom', 'apollo-events' ); ?></label>
				<input type="text" id="event_coupon_code" name="coupon_code" class="a-eve-form__input" placeholder="APOLLO20">
			</div>
			<div class="a-eve-form__group a-eve-form__group--half">
				<label for="event_list_url"><?php esc_html_e( 'URL Lista Amiga', 'apollo-events' ); ?></label>
				<input type="url" id="event_list_url" name="list_url" class="a-eve-form__input" placeholder="https://...">
			</div>
		</div>

		<div class="a-eve-form__group">
			<label for="event_privacy"><?php esc_html_e( 'Privacidade', 'apollo-events' ); ?></label>
			<select id="event_privacy" name="privacy" class="a-eve-form__input">
				<option value="public"><?php esc_html_e( 'Público', 'apollo-events' ); ?></option>
				<option value="private"><?php esc_html_e( 'Privado', 'apollo-events' ); ?></option>
				<option value="invite"><?php esc_html_e( 'Apenas Convidados', 'apollo-events' ); ?></option>
			</select>
		</div>

		<div class="a-eve-form__row">
			<div class="a-eve-form__group a-eve-form__group--half">
				<label for="event_status"><?php esc_html_e( 'Status do Evento', 'apollo-events' ); ?></label>
				<select id="event_status" name="event_status" class="a-eve-form__input">
					<option value="scheduled"><?php esc_html_e( 'Agendado', 'apollo-events' ); ?></option>
					<option value="ongoing"><?php esc_html_e( 'Em andamento', 'apollo-events' ); ?></option>
					<option value="postponed"><?php esc_html_e( 'Adiado', 'apollo-events' ); ?></option>
					<option value="cancelled"><?php esc_html_e( 'Cancelado', 'apollo-events' ); ?></option>
					<option value="finished"><?php esc_html_e( 'Finalizado', 'apollo-events' ); ?></option>
				</select>
			</div>
			<div class="a-eve-form__group a-eve-form__group--half">
				<label for="event_post_status"><?php esc_html_e( 'Publicação', 'apollo-events' ); ?></label>
				<select id="event_post_status" name="post_status" class="a-eve-form__input">
					<option value="publish"><?php esc_html_e( 'Publicar agora', 'apollo-events' ); ?></option>
					<option value="pending"><?php esc_html_e( 'Enviar para revisão', 'apollo-events' ); ?></option>
					<option value="draft"><?php esc_html_e( 'Salvar rascunho', 'apollo-events' ); ?></option>
				</select>
			</div>
		</div>

		<div class="a-eve-form__actions">
			<button type="submit" class="a-eve-form__submit">
				<?php esc_html_e( 'Criar Evento', 'apollo-events' ); ?>
			</button>
		</div>

		<div id="a-eve-form-message" class="a-eve-form__message" style="display:none;"></div>
	</form>
</div>