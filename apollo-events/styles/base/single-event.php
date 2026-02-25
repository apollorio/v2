<?php

/**
 * Template: Single Event — Base Style
 *
 * @package Apollo\Event
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
	<meta name="theme-color" content="#ffffff">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="default">
	<title><?php the_title(); ?> - Apollo::Rio</title>

	<!-- Apollo CDN - Mandatory for all pages -->
	<script src="https://cdn.apollo.rio.br/v1.0.0/core.min.js?v=1.0.0" fetchpriority="high"></script>

	<!-- Page styles -->
	<style>
		html,
		body {
			margin: 0;
			padding: 0;
			background: #fff;
			color: #000;
			font-family: 'Manrope', sans-serif;
			line-height: 1.5;
		}
	</style>
</head>

<body>
	<?php

	$post_id      = get_the_ID();
	$start_date   = get_post_meta( $post_id, '_event_start_date', true );
	$end_date     = get_post_meta( $post_id, '_event_end_date', true );
	$start_time   = get_post_meta( $post_id, '_event_start_time', true );
	$end_time     = get_post_meta( $post_id, '_event_end_time', true );
	$parsed_date  = apollo_event_parse_date( $start_date ?: current_time( 'Y-m-d' ) );
	$loc          = apollo_event_get_loc( $post_id );
	$djs          = apollo_event_get_djs( $post_id );
	$banner       = apollo_event_get_banner( $post_id );
	$ticket_url   = get_post_meta( $post_id, '_event_ticket_url', true );
	$ticket_price = get_post_meta( $post_id, '_event_ticket_price', true );
	$privacy      = get_post_meta( $post_id, '_event_privacy', true ) ?: 'public';
	$status       = get_post_meta( $post_id, '_event_status', true ) ?: 'scheduled';
	$is_gone      = apollo_event_is_gone( $post_id );
	$sounds       = wp_get_post_terms( $post_id, APOLLO_EVENT_TAX_SOUND, array( 'fields' => 'names' ) );
	$categories   = wp_get_post_terms( $post_id, APOLLO_EVENT_TAX_CATEGORY, array( 'fields' => 'names' ) );

	$gone_class = $is_gone ? ' apollo-event-gone' : '';

	wp_enqueue_style( 'apollo-events' );
	wp_enqueue_script( 'apollo-events' );

	// Mapa se tiver localização
	if ( $loc && ! empty( $loc['lat'] ) && apollo_event_option( 'enable_osm_map', true ) ) {
		wp_enqueue_style( 'leaflet' );
		wp_enqueue_script( 'leaflet' );
		wp_enqueue_script( 'apollo-events-map' );
	}
	?>

	<div class="a-eve-single<?php echo esc_attr( $gone_class ); ?>">

		<!-- Banner -->
		<?php if ( $banner ) : ?>
			<div class="a-eve-single__banner">
				<img src="<?php echo esc_url( $banner ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
				<?php if ( 'scheduled' !== $status ) : ?>
					<span class="a-eve-card__status a-eve-card__status--<?php echo esc_attr( $status ); ?>">
						<?php echo esc_html( ucfirst( $status ) ); ?>
					</span>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<!-- Header -->
		<div class="a-eve-single__header">
			<?php if ( ! empty( $categories ) ) : ?>
				<div class="a-eve-single__categories">
					<?php foreach ( $categories as $cat ) : ?>
						<span class="a-eve-single__category"><?php echo esc_html( $cat ); ?></span>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<h1 class="a-eve-single__title"><?php the_title(); ?></h1>

			<?php if ( $is_gone ) : ?>
				<div class="a-eve-single__gone-notice">
					⚠ <?php esc_html_e( 'Este evento já encerrou.', 'apollo-events' ); ?>
				</div>
			<?php endif; ?>
		</div>

		<!-- Content Layout -->
		<div class="a-eve-single__layout">

			<!-- Main Content -->
			<div class="a-eve-single__content">
				<?php the_content(); ?>

				<!-- DJs Line-up -->
				<?php if ( ! empty( $djs ) ) : ?>
					<div class="a-eve-single__section">
						<h3>🎧 <?php esc_html_e( 'Line-up', 'apollo-events' ); ?></h3>
						<div class="a-eve-single__djs">
							<?php foreach ( $djs as $dj ) : ?>
								<a href="<?php echo esc_url( $dj['permalink'] ); ?>" class="a-eve-single__dj">
									<?php if ( ! empty( $dj['thumbnail'] ) ) : ?>
										<img src="<?php echo esc_url( $dj['thumbnail'] ); ?>" alt="<?php echo esc_attr( $dj['title'] ); ?>" class="a-eve-single__dj-avatar">
									<?php endif; ?>
									<span class="a-eve-single__dj-name"><?php echo esc_html( $dj['title'] ); ?></span>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<!-- Sound Tags -->
				<?php if ( ! empty( $sounds ) ) : ?>
					<div class="a-eve-single__section">
						<h3>🎵 <?php esc_html_e( 'Estilos Musicais', 'apollo-events' ); ?></h3>
						<div class="a-eve-card__sounds">
							<?php foreach ( $sounds as $sound ) : ?>
								<span class="a-eve-card__sound-tag"><?php echo esc_html( $sound ); ?></span>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php
				// Integrations hooks
				do_action( 'apollo_event_single_after_content', $post_id );
				?>
			</div>

			<!-- Sidebar -->
			<aside class="a-eve-single__sidebar">
				<!-- Date & Time -->
				<div class="a-eve-single__info-box">
					<h4>📅 <?php esc_html_e( 'Data e Horário', 'apollo-events' ); ?></h4>
					<p>
						<?php
						if ( ! empty( $parsed_date ) ) {
							echo esc_html( $parsed_date['dia_semana'] . ', ' . $parsed_date['dia'] . ' de ' . $parsed_date['mes'] . ' de ' . $parsed_date['ano'] );
						}
						?>
					</p>
					<?php if ( $end_date && $end_date !== $start_date ) : ?>
						<p><?php esc_html_e( 'até', 'apollo-events' ); ?> <?php echo esc_html( apollo_event_parse_date( $end_date )['dia'] . ' de ' . apollo_event_parse_date( $end_date )['mes'] ); ?></p>
					<?php endif; ?>
					<?php if ( $start_time ) : ?>
						<p>🕐 <?php echo esc_html( $start_time ); ?><?php echo $end_time ? ' – ' . esc_html( $end_time ) : ''; ?></p>
					<?php endif; ?>
				</div>

				<!-- Location -->
				<?php if ( $loc ) : ?>
					<div class="a-eve-single__info-box">
						<h4>📍 <?php esc_html_e( 'Local', 'apollo-events' ); ?></h4>
						<p><a href="<?php echo esc_url( $loc['permalink'] ); ?>"><?php echo esc_html( $loc['title'] ); ?></a></p>
						<?php if ( ! empty( $loc['address'] ) ) : ?>
							<p class="a-eve-single__address"><?php echo esc_html( $loc['address'] ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $loc['lat'] ) && apollo_event_option( 'enable_osm_map', true ) ) : ?>
							<div class="a-eve-single__mini-map"
								id="a-eve-single-map"
								data-lat="<?php echo esc_attr( $loc['lat'] ); ?>"
								data-lng="<?php echo esc_attr( $loc['lng'] ); ?>"
								data-title="<?php echo esc_attr( $loc['title'] ); ?>"
								style="height:200px;border-radius:var(--a-eve-radius);margin-top:8px;">
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<!-- Ticket -->
				<?php if ( $ticket_url || $ticket_price ) : ?>
					<div class="a-eve-single__info-box">
						<h4>🎟 <?php esc_html_e( 'Ingressos', 'apollo-events' ); ?></h4>
						<?php if ( $ticket_price ) : ?>
							<p class="a-eve-single__price"><?php echo esc_html( $ticket_price ); ?></p>
						<?php endif; ?>
						<?php if ( $ticket_url && ! $is_gone ) : ?>
							<a href="<?php echo esc_url( $ticket_url ); ?>" class="a-eve-card__ticket" target="_blank" rel="noopener">
								<?php esc_html_e( 'Comprar Ingressos', 'apollo-events' ); ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<!-- Privacy -->
				<?php if ( 'public' !== $privacy ) : ?>
					<div class="a-eve-single__info-box">
						<p>🔒 <?php echo 'private' === $privacy ? esc_html__( 'Evento Privado', 'apollo-events' ) : esc_html__( 'Apenas Convidados', 'apollo-events' ); ?></p>
					</div>
				<?php endif; ?>

				<?php do_action( 'apollo_event_single_sidebar', $post_id ); ?>
			</aside>
		</div>

		<?php do_action( 'apollo_event_single_after', $post_id ); ?>

		<!-- ═══════════════════════════════════════════════════════════════
		TICKET RESALE — apollo-adverts (classifieds linked to event)
		═══════════════════════════════════════════════════════════════ -->
		<?php
		$resale_args  = array(
			'post_type'      => 'classified',
			'post_status'    => 'publish',
			'posts_per_page' => 6,
			'meta_query'     => array(
				array(
					'key'   => '_classified_event_id',
					'value' => $post_id,
					'type'  => 'NUMERIC',
				),
			),
		);
		$resale_query = new \WP_Query( $resale_args );

		if ( $resale_query->have_posts() ) :
			?>
			<div class="a-eve-single__section a-eve-resale">
				<div class="a-eve-resale__header">
					<h3 class="a-eve-resale__title">🎟 <?php esc_html_e( 'Ingressos à venda por usuários', 'apollo-events' ); ?></h3>
					<span class="a-eve-resale__count"><?php echo esc_html( $resale_query->found_posts ); ?> <?php esc_html_e( 'ofertas', 'apollo-events' ); ?></span>
				</div>

				<div class="a-eve-resale__grid">
					<?php
					while ( $resale_query->have_posts() ) :
						$resale_query->the_post();
						$r_id       = get_the_ID();
						$r_price    = get_post_meta( $r_id, '_classified_price', true );
						$r_currency = get_post_meta( $r_id, '_classified_currency', true ) ?: 'BRL';
						$r_neg      = get_post_meta( $r_id, '_classified_negotiable', true );
						$r_author   = get_the_author();
						$r_avatar   = get_avatar_url( get_the_author_meta( 'ID' ), array( 'size' => 80 ) );
						$r_date     = get_the_time( 'Y-m-d H:i:s' );
						?>
						<a href="<?php the_permalink(); ?>" class="a-eve-resale__card">
							<div class="a-eve-resale__card-top">
								<img src="<?php echo esc_url( $r_avatar ); ?>" alt="<?php echo esc_attr( $r_author ); ?>" class="a-eve-resale__avatar" />
								<div class="a-eve-resale__seller">
									<span class="a-eve-resale__seller-name"><?php echo esc_html( $r_author ); ?></span>
									<span class="a-eve-resale__seller-time"><?php echo wp_kses_post( apollo_time_ago_html( $r_date ) ); ?></span>
								</div>
							</div>
							<h4 class="a-eve-resale__card-title"><?php the_title(); ?></h4>
							<div class="a-eve-resale__card-bottom">
								<?php if ( $r_price ) : ?>
									<span class="a-eve-resale__price"><?php echo esc_html( $r_currency . ' ' . number_format( (float) $r_price, 0, ',', '.' ) ); ?></span>
								<?php endif; ?>
								<?php if ( $r_neg ) : ?>
									<span class="a-eve-resale__neg"><?php esc_html_e( 'Negociável', 'apollo-events' ); ?></span>
								<?php endif; ?>
							</div>
						</a>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		<?php endif; ?>

		<!-- ═══════════════════════════════════════════════════════════════
		DEPOIMENTOS — Timeline (comments=true)
		═══════════════════════════════════════════════════════════════ -->
		<?php if ( comments_open( $post_id ) || get_comments_number( $post_id ) ) : ?>
			<div class="a-eve-single__section a-eve-depoimentos">
				<div class="a-eve-depo__header">
					<h3 class="a-eve-depo__title">💬 <?php esc_html_e( 'Depoimentos', 'apollo-events' ); ?></h3>
					<span class="a-eve-depo__count"><?php echo esc_html( get_comments_number( $post_id ) ); ?></span>
				</div>

				<?php
				$depoimentos = get_comments(
					array(
						'post_id' => $post_id,
						'status'  => 'approve',
						'number'  => 10,
						'orderby' => 'comment_date',
						'order'   => 'DESC',
					)
				);
				?>

				<?php if ( ! empty( $depoimentos ) ) : ?>
					<div class="a-eve-depo__timeline">
						<?php
						foreach ( $depoimentos as $idx => $depo ) :
							$d_avatar = get_avatar_url( $depo->comment_author_email, array( 'size' => 80 ) );
							$d_time   = $depo->comment_date;
							?>
							<div class="a-eve-depo__item<?php echo $idx === 0 ? ' is-latest' : ''; ?>">
								<div class="a-eve-depo__line">
									<span class="a-eve-depo__dot"></span>
									<?php if ( $idx < count( $depoimentos ) - 1 ) : ?>
										<span class="a-eve-depo__connector"></span>
									<?php endif; ?>
								</div>
								<div class="a-eve-depo__card">
									<div class="a-eve-depo__card-header">
										<img src="<?php echo esc_url( $d_avatar ); ?>" alt="" class="a-eve-depo__avatar" />
										<div class="a-eve-depo__meta">
											<span class="a-eve-depo__author"><?php echo esc_html( $depo->comment_author ); ?></span>
											<span class="a-eve-depo__time"><?php echo wp_kses_post( apollo_time_ago_html( $d_time ) ); ?></span>
										</div>
									</div>
									<p class="a-eve-depo__text"><?php echo wp_kses_post( $depo->comment_content ); ?></p>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<!-- Comment Form -->
				<?php if ( comments_open( $post_id ) && is_user_logged_in() ) : ?>
					<div class="a-eve-depo__form-wrap">
						<?php
						comment_form(
							array(
								'title_reply'          => '',
								'comment_notes_before' => '',
								'comment_notes_after'  => '',
								'label_submit'         => __( 'Enviar Depoimento', 'apollo-events' ),
								'comment_field'        => '<div class="a-eve-depo__input-wrap"><textarea name="comment" class="a-eve-depo__input" placeholder="' . esc_attr__( 'Compartilhe sua experiência...', 'apollo-events' ) . '" rows="3" required></textarea></div>',
								'class_form'           => 'a-eve-depo__form',
								'class_submit'         => 'a-eve-depo__submit',
							),
							$post_id
						);
						?>
					</div>
				<?php elseif ( ! is_user_logged_in() && comments_open( $post_id ) ) : ?>
					<p class="a-eve-depo__login-cta">
						<a href="<?php echo esc_url( wp_login_url( get_permalink( $post_id ) ) ); ?>"><?php esc_html_e( 'Faça login para deixar um depoimento', 'apollo-events' ); ?></a>
					</p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	</div>

	<?php if ( is_user_logged_in() ) : ?>
		<!-- Apollo Navbar -->
		<?php include plugin_dir_path( __DIR__ ) . '../../apollo-templates/templates/template-parts/navbar.php'; ?>
	<?php endif; ?>

</body>

</html>