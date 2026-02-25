<?php
/**
 * Template: Archive Event — Base Style
 *
 * @package Apollo\Event
 */

if ( ! defined( 'ABSPATH' ) ) exit;

get_header();

wp_enqueue_style( 'apollo-events' );
wp_enqueue_style( 'apollo-events-cards' );
wp_enqueue_style( 'apollo-events-calendar' );
wp_enqueue_script( 'apollo-events' );
wp_enqueue_script( 'apollo-events-calendar' );

$style       = apollo_event_get_active_style();
$default_view = apollo_event_option( 'default_view', 'card' );

// Enqueue style CSS
$style_css = APOLLO_EVENT_DIR . 'styles/' . $style . '/style.css';
if ( file_exists( $style_css ) ) {
	wp_enqueue_style( 'apollo-events-style-' . $style, APOLLO_EVENT_URL . 'styles/' . $style . '/style.css', [ 'apollo-events' ], APOLLO_EVENT_VERSION );
}
?>

<div class="a-eve-archive">
	<div class="a-eve-archive__header">
		<h1 class="a-eve-archive__title"><?php post_type_archive_title(); ?></h1>

		<!-- Event Search -->
		<div class="a-eve-archive__search">
			<input
				type="text"
				data-apollo-search="events"
				data-search-navigate="true"
				data-search-min="2"
				data-search-limit="8"
				placeholder="<?php esc_attr_e( 'Buscar evento...', 'apollo-events' ); ?>"
				class="a-eve-search-input"
				autocomplete="off"
			/>
		</div>

		<!-- View Switcher -->
		<div class="a-eve-archive__views">
			<button class="a-eve-view-btn<?php echo 'card' === $default_view ? ' a-eve-view-btn--active' : ''; ?>" data-view="card" aria-label="Cards">▦</button>
			<button class="a-eve-view-btn<?php echo 'list' === $default_view ? ' a-eve-view-btn--active' : ''; ?>" data-view="list" aria-label="Lista">☰</button>
			<?php if ( apollo_event_option( 'enable_osm_map', true ) ) : ?>
				<button class="a-eve-view-btn<?php echo 'map' === $default_view ? ' a-eve-view-btn--active' : ''; ?>" data-view="map" aria-label="Mapa">🗺</button>
			<?php endif; ?>
		</div>
	</div>

	<!-- Events Grid -->
	<div class="a-eve-events a-eve-events--<?php echo esc_attr( $default_view ); ?>" id="a-eve-events-container">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();

				$event_data = [
					'id'           => get_the_ID(),
					'title'        => get_the_title(),
					'permalink'    => get_permalink(),
					'excerpt'      => get_the_excerpt(),
					'banner'       => apollo_event_get_banner( get_the_ID() ),
					'start_date'   => get_post_meta( get_the_ID(), '_event_start_date', true ),
					'end_date'     => get_post_meta( get_the_ID(), '_event_end_date', true ),
					'start_time'   => get_post_meta( get_the_ID(), '_event_start_time', true ),
					'end_time'     => get_post_meta( get_the_ID(), '_event_end_time', true ),
					'parsed_date'  => apollo_event_parse_date( get_post_meta( get_the_ID(), '_event_start_date', true ) ?: current_time( 'Y-m-d' ) ),
					'djs'          => apollo_event_get_djs( get_the_ID() ),
					'dj_names'     => implode( ', ', array_column( apollo_event_get_djs( get_the_ID() ), 'title' ) ),
					'loc'          => apollo_event_get_loc( get_the_ID() ),
					'loc_name'     => apollo_event_get_loc( get_the_ID() ) ? apollo_event_get_loc( get_the_ID() )['title'] : '',
					'ticket_url'   => get_post_meta( get_the_ID(), '_event_ticket_url', true ),
					'ticket_price' => get_post_meta( get_the_ID(), '_event_ticket_price', true ),
					'privacy'      => get_post_meta( get_the_ID(), '_event_privacy', true ) ?: 'public',
					'status'       => get_post_meta( get_the_ID(), '_event_status', true ) ?: 'scheduled',
					'is_gone'      => apollo_event_is_gone( get_the_ID() ),
					'sounds'       => wp_get_post_terms( get_the_ID(), APOLLO_EVENT_TAX_SOUND, [ 'fields' => 'names' ] ),
					'categories'   => wp_get_post_terms( get_the_ID(), APOLLO_EVENT_TAX_CATEGORY, [ 'fields' => 'names' ] ),
				];

				// Ocultar gone se configurado
				if ( $event_data['is_gone'] && ! apollo_event_option( 'show_gone_events', true ) ) {
					continue;
				}

				// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
				extract( $event_data, EXTR_SKIP );

				$template_name = 'event-' . $default_view . '.php';
				$template_path = APOLLO_EVENT_DIR . 'styles/' . $style . '/' . $template_name;
				if ( ! file_exists( $template_path ) ) {
					$template_path = APOLLO_EVENT_DIR . 'styles/base/' . $template_name;
				}
				if ( file_exists( $template_path ) ) {
					include $template_path;
				}

			endwhile;
		else :
			echo '<p class="a-eve-empty">' . esc_html__( 'Nenhum evento encontrado.', 'apollo-events' ) . '</p>';
		endif;
		?>
	</div>

	<!-- Pagination -->
	<div class="a-eve-archive__pagination">
		<?php
		the_posts_pagination( [
			'mid_size'  => 2,
			'prev_text' => '← ' . __( 'Anterior', 'apollo-events' ),
			'next_text' => __( 'Próximo', 'apollo-events' ) . ' →',
		] );
		?>
	</div>
</div>

<?php
get_footer();
