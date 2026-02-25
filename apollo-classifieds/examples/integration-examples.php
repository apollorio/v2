<?php

/**
 * EXAMPLE: How to Use Classifieds System in Other Pages/Plugins
 *
 * This file demonstrates how to integrate the modular classifieds
 * template parts into custom pages/shortcodes/widgets.
 *
 * @package Apollo\Classifieds
 */

// ==========================================
// EXAMPLE 1: Custom Page Template
// ==========================================

/**
 * File: wp-content/themes/your-theme/page-marketplace-custom.php
 *
 * Create a custom page template that shows only specific classified types.
 */

/*
Template Name: Marketplace Custom
*/

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Marketplace - Apollo::Rio</title>
	<script src="https://cdn.apollo.rio.br/v1.0.0/core.min.js?v=1.0.0" fetchpriority="high"></script>
</head>

<body>

	<main class="container">

		<?php
		// Load security info box
		get_template_part( 'wp-content/plugins/apollo-classifieds/templates/parts/info-box' );
		?>

		<?php
		// ONLY show tickets
		$tickets_query = new WP_Query(
			array(
				'post_type'      => 'classified',
				'posts_per_page' => 20,
				'meta_query'     => array(
					array(
						'key'     => '_classified_type',
						'value'   => 'ticket',
						'compare' => '=',
					),
				),
			)
		);

		if ( $tickets_query->have_posts() ) :
			get_template_part(
				'wp-content/plugins/apollo-classifieds/templates/parts/section-header',
				null,
				array(
					'icon'  => 'ri-ticket-2-fill',
					'title' => 'Todos os Ingressos',
					'count' => $tickets_query->found_posts . ' Disponíveis',
				)
			);
			?>

			<div class="grid-layout">
				<?php
				while ( $tickets_query->have_posts() ) :
					$tickets_query->the_post();
					get_template_part( 'wp-content/plugins/apollo-classifieds/templates/parts/card-ticket' );
				endwhile;
				?>
			</div>

			<?php
		endif;
		wp_reset_postdata();
		?>

	</main>

	<?php
	// ALWAYS include modal at end
	get_template_part( 'wp-content/plugins/apollo-classifieds/templates/parts/modal-disclaimer' );
	get_footer();


	// ==========================================
	// EXAMPLE 2: Shortcode Integration
	// ==========================================

	/**
	 * Add to functions.php or plugin file:
	 */

	add_shortcode(
		'apollo_tickets',
		function ( $atts ) {
			$atts = shortcode_atts(
				array(
					'limit'       => 12,
					'event'       => '', // Filter by event name
					'show_header' => 'yes',
				),
				$atts
			);

			$args = array(
				'post_type'      => 'classified',
				'posts_per_page' => intval( $atts['limit'] ),
				'meta_query'     => array(
					array(
						'key'     => '_classified_type',
						'value'   => 'ticket',
						'compare' => '=',
					),
				),
			);

			if ( $atts['event'] ) {
				$args['meta_query'][] = array(
					'key'     => '_event_title',
					'value'   => $atts['event'],
					'compare' => 'LIKE',
				);
			}

			$query = new WP_Query( $args );

			ob_start();

			if ( $query->have_posts() ) :
				if ( $atts['show_header'] === 'yes' ) {
					get_template_part(
						'wp-content/plugins/apollo-classifieds/templates/parts/section-header',
						null,
						array(
							'icon'  => 'ri-ticket-2-fill',
							'title' => 'Ingressos Disponíveis',
							'count' => $query->found_posts . ' Active',
						)
					);
				}
				?>

			<div class="grid-layout">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					get_template_part( 'wp-content/plugins/apollo-classifieds/templates/parts/card-ticket' );
					endwhile;
				?>
			</div>

				<?php
		endif;
			wp_reset_postdata();

			return ob_get_clean();
		}
	);

	/**
	 * Usage in post/page editor:
	 * [apollo_tickets limit="6" event="Sunset Theory"]
	 */


	// ==========================================
	// EXAMPLE 3: Widget (Gutenberg Block)
	// ==========================================

	/**
	 * Register a simple block that shows recent tickets
	 */

	add_action(
		'init',
		function () {
			if ( ! function_exists( 'register_block_type' ) ) {
				return;
			}

			register_block_type(
				'apollo/recent-tickets',
				array(
					'render_callback' => function ( $attributes ) {
						$query = new WP_Query(
							array(
								'post_type'      => 'classified',
								'posts_per_page' => 3,
								'meta_query'     => array(
									array(
										'key'   => '_classified_type',
										'value' => 'ticket',
									),
								),
							)
						);

						ob_start();

						if ( $query->have_posts() ) :
							echo '<div class="grid-layout" style="grid-template-columns: repeat(3, 1fr);">';
							while ( $query->have_posts() ) :
								$query->the_post();
								get_template_part( 'wp-content/plugins/apollo-classifieds/templates/parts/card-ticket' );
							endwhile;
							echo '</div>';
				endif;

						wp_reset_postdata();
						return ob_get_clean();
					},
				)
			);
		}
	);


	// ==========================================
	// EXAMPLE 4: REST API Endpoint
	// ==========================================

	/**
	 * Create custom endpoint to fetch classifieds via AJAX/fetch
	 */

	add_action(
		'rest_api_init',
		function () {
			register_rest_route(
				'apollo/v1',
				'/classifieds/(?P<type>[a-zA-Z]+)',
				array(
					'methods'             => 'GET',
					'callback'            => function ( $request ) {
						$type = sanitize_text_field( $request['type'] );

						$query = new WP_Query(
							array(
								'post_type'      => 'classified',
								'posts_per_page' => 50,
								'meta_query'     => array(
									array(
										'key'     => '_classified_type',
										'value'   => $type,
										'compare' => '=',
									),
								),
							)
						);

						$classifieds = array();

						while ( $query->have_posts() ) :
							$query->the_post();
							$classifieds[] = array(
								'id'          => get_the_ID(),
								'title'       => get_the_title(),
								'author'      => array(
									'id'       => get_the_author_meta( 'ID' ),
									'name'     => get_the_author_meta( 'display_name' ),
									'username' => get_the_author_meta( 'user_login' ),
									'avatar'   => get_avatar_url( get_the_author_meta( 'ID' ) ),
								),
								'price'       => get_post_meta( get_the_ID(), '_price', true ),
								'event_title' => get_post_meta( get_the_ID(), '_event_title', true ),
								'event_date'  => get_post_meta( get_the_ID(), '_event_date', true ),
								'location'    => get_post_meta( get_the_ID(), '_event_location', true ) ?: get_post_meta( get_the_ID(), '_location', true ),
								'image'       => get_the_post_thumbnail_url( get_the_ID(), 'medium' ),
							);
				endwhile;

						wp_reset_postdata();

						return rest_ensure_response( $classifieds );
					},
					'permission_callback' => '__return_true',
				)
			);
		}
	);

	/**
	 * Usage: GET /wp-json/apollo/v1/classifieds/ticket
	 * Returns JSON array of ticket classifieds
	 */


	// ==========================================
	// EXAMPLE 5: User Dashboard Integration
	// ==========================================

	/**
	 * Show user's own classifieds in apollo-dashboard
	 */

	add_action(
		'apollo_dashboard_content_classifieds',
		function () {
			$user_classifieds = new WP_Query(
				array(
					'post_type'      => 'classified',
					'author'         => get_current_user_id(),
					'posts_per_page' => -1,
				)
			);

			if ( $user_classifieds->have_posts() ) :
				echo '<h2>Meus Classificados</h2>';
				echo '<div class="grid-layout">';

				while ( $user_classifieds->have_posts() ) :
					$user_classifieds->the_post();
					$type = get_post_meta( get_the_ID(), '_classified_type', true );

					if ( $type === 'ticket' ) {
						get_template_part( 'wp-content/plugins/apollo-classifieds/templates/parts/card-ticket' );
					} elseif ( $type === 'accommodation' ) {
						get_template_part( 'wp-content/plugins/apollo-classifieds/templates/parts/card-accommodation' );
					}
				endwhile;

				echo '</div>';
		else :
			echo '<p>Você ainda não publicou nenhum classificado.</p>';
		endif;

		wp_reset_postdata();
		}
	);


	// ==========================================
	// NOTES
	// ==========================================

	/*
	1. ALWAYS enqueue classifieds.css when using template parts
	2. ALWAYS include modal-disclaimer.php at page bottom
	3. Each card has data-user-id and data-classified-id for AJAX
	4. Modal flow: Click button → Modal opens → Check consent → Chat unlocks
	5. NEVER open chat directly without modal disclaimer
	*/
