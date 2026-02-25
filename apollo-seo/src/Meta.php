<?php

/**
 * Apollo SEO — Meta Tags Engine
 *
 * Generates and outputs ALL SEO meta tags: title, description,
 * canonical, robots, Open Graph, Twitter Cards.
 * Works with standard WP pages AND blank canvas templates.
 *
 * Adapted from The SEO Framework's architecture —
 * simplified for Apollo's specific CPT ecosystem.
 *
 * @package Apollo\SEO
 */

declare(strict_types=1);

namespace Apollo\SEO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Meta {


	/*
	═══════════════════════════════════════════════════════════════
		TITLE ENGINE
		═══════════════════════════════════════════════════════════════ */

	/**
	 * Filter for pre_get_document_title.
	 */
	public static function filter_document_title( string $title ): string {
		return self::build_title();
	}

	/**
	 * Filter for document_title_parts.
	 */
	public static function filter_title_parts( array $parts ): array {
		$title = self::build_title();
		return array( 'title' => $title );
	}

	/**
	 * Build the full <title> string.
	 */
	public static function build_title( array $args = array() ): string {
		$sep       = Settings::separator();
		$site_name = Settings::get( 'site_title' ) ?: get_bloginfo( 'name' );
		$location  = Settings::get( 'title_location', 'right' );

		/* Custom title from post meta */
		$custom = self::get_custom_title( $args );
		if ( $custom ) {
			$page_title = $custom;
		} else {
			$page_title = self::get_generated_title( $args );
		}

		if ( ! $page_title ) {
			$page_title = $site_name;
			return $page_title;
		}

		/* Branding */
		if ( $location === 'right' ) {
			$full = $page_title . ' ' . $sep . ' ' . $site_name;
		} else {
			$full = $site_name . ' ' . $sep . ' ' . $page_title;
		}

		return self::sanitize_title( $full );
	}

	/**
	 * Get custom title from post/term meta.
	 */
	private static function get_custom_title( array $args = array() ): string {
		if ( is_singular() || ! empty( $args['post_id'] ) ) {
			$id = $args['post_id'] ?? get_the_ID();
			if ( $id ) {
				$custom = Settings::get_post_meta( (int) $id, 'title' );
				if ( $custom ) {
					return $custom;
				}
			}
		}

		if ( is_tax() || is_category() || is_tag() ) {
			$term = get_queried_object();
			if ( $term && isset( $term->term_id ) ) {
				$custom = Settings::get_term_meta( $term->term_id, 'title' );
				if ( $custom ) {
					return $custom;
				}
			}
		}

		return '';
	}

	/**
	 * Auto-generate title from context.
	 */
	private static function get_generated_title( array $args = array() ): string {
		/* Homepage */
		if ( is_front_page() || is_home() ) {
			$ht = Settings::get( 'homepage_title' );
			return $ht ?: get_bloginfo( 'name' );
		}

		/* Archive titles from settings */
		if ( is_post_type_archive( 'event' ) ) {
			return Settings::get( 'archive_title_event', 'Eventos' );
		}
		if ( is_post_type_archive( 'dj' ) ) {
			return Settings::get( 'archive_title_dj', 'DJs' );
		}
		if ( is_post_type_archive( 'local' ) ) {
			return Settings::get( 'archive_title_loc', 'GPS' );
		}
		if ( is_post_type_archive( 'classified' ) ) {
			return Settings::get( 'archive_title_classified', 'Classificados' );
		}

		/* Singular */
		if ( is_singular() ) {
			$post = get_queried_object();
			if ( $post ) {
				return get_the_title( $post );
			}
		}

		/* Taxonomy */
		if ( is_tax() || is_category() || is_tag() ) {
			$term = get_queried_object();
			if ( $term ) {
				return $term->name;
			}
		}

		/* Author */
		if ( is_author() ) {
			$author = get_queried_object();
			return $author->display_name ?? 'Perfil';
		}

		/* Search */
		if ( is_search() ) {
			return 'Busca: ' . get_search_query();
		}

		/* 404 */
		if ( is_404() ) {
			return 'Página não encontrada';
		}

		return '';
	}

	/**
	 * Sanitize title string.
	 */
	private static function sanitize_title( string $title ): string {
		$title = wp_strip_all_tags( $title );
		$title = html_entity_decode( $title, ENT_QUOTES, 'UTF-8' );
		$title = preg_replace( '/\s+/', ' ', $title );
		return trim( $title );
	}

	/*
	═══════════════════════════════════════════════════════════════
		DESCRIPTION ENGINE
		═══════════════════════════════════════════════════════════════ */

	/**
	 * Build meta description.
	 */
	public static function build_description( array $args = array() ): string {
		/* Custom from meta */
		$custom = self::get_custom_description( $args );
		if ( $custom ) {
			return self::clamp( $custom, 160 );
		}

		return self::clamp( self::get_generated_description( $args ), 160 );
	}

	/**
	 * Custom description from post/term meta.
	 */
	private static function get_custom_description( array $args = array() ): string {
		if ( is_singular() || ! empty( $args['post_id'] ) ) {
			$id = $args['post_id'] ?? get_the_ID();
			if ( $id ) {
				return Settings::get_post_meta( (int) $id, 'description' ) ?: '';
			}
		}

		if ( is_tax() || is_category() || is_tag() ) {
			$term = get_queried_object();
			if ( $term && isset( $term->term_id ) ) {
				return Settings::get_term_meta( $term->term_id, 'description' ) ?: '';
			}
		}

		return '';
	}

	/**
	 * Auto-generate description from content.
	 */
	private static function get_generated_description( array $args = array() ): string {
		/* Homepage */
		if ( is_front_page() || is_home() ) {
			return Settings::get( 'homepage_desc' ) ?: get_bloginfo( 'description' );
		}

		/* Archives */
		if ( is_post_type_archive( 'event' ) ) {
			return Settings::get( 'archive_desc_event' );
		}
		if ( is_post_type_archive( 'dj' ) ) {
			return Settings::get( 'archive_desc_dj' );
		}
		if ( is_post_type_archive( 'local' ) ) {
			return Settings::get( 'archive_desc_loc' );
		}
		if ( is_post_type_archive( 'classified' ) ) {
			return Settings::get( 'archive_desc_classified' );
		}

		/* Singular — excerpt or content */
		if ( is_singular() ) {
			$post = get_queried_object();
			if ( $post ) {
				return self::extract_excerpt( $post );
			}
		}

		/* Taxonomy */
		if ( is_tax() || is_category() || is_tag() ) {
			$term = get_queried_object();
			if ( $term && $term->description ) {
				return $term->description;
			}
			return sprintf( '%s — conteúdo selecionado na Apollo::Rio', $term->name ?? '' );
		}

		/* Author */
		if ( is_author() ) {
			$author = get_queried_object();
			$bio    = get_the_author_meta( 'description', $author->ID ?? 0 );
			return $bio ?: 'Perfil de membro da comunidade Apollo::Rio';
		}

		return '';
	}

	/**
	 * Extract excerpt from post content.
	 */
	private static function extract_excerpt( \WP_Post $post ): string {
		if ( ! empty( $post->post_excerpt ) ) {
			return wp_strip_all_tags( $post->post_excerpt );
		}

		$content = $post->post_content;
		$content = strip_shortcodes( $content );
		$content = wp_strip_all_tags( $content );
		$content = preg_replace( '/\s+/', ' ', $content );

		return trim( $content );
	}

	/**
	 * Clamp text to N characters at sentence boundary.
	 */
	private static function clamp( string $text, int $max = 160 ): string {
		$text = wp_strip_all_tags( $text );
		$text = preg_replace( '/\s+/', ' ', trim( $text ) );

		if ( mb_strlen( $text ) <= $max ) {
			return $text;
		}

		$cut = mb_substr( $text, 0, $max );
		$pos = mb_strrpos( $cut, '.' );
		if ( $pos !== false && $pos > $max * 0.5 ) {
			return mb_substr( $cut, 0, $pos + 1 );
		}

		$pos = mb_strrpos( $cut, ' ' );
		if ( $pos !== false ) {
			return mb_substr( $cut, 0, $pos ) . '…';
		}

		return $cut . '…';
	}

	/*
	═══════════════════════════════════════════════════════════════
		IMAGE ENGINE
		═══════════════════════════════════════════════════════════════ */

	/**
	 * Resolve OG image URL with fallback chain.
	 *
	 * 1. Custom social image (post meta)
	 * 2. Featured image
	 * 3. First image in content
	 * 4. Default OG image from settings
	 */
	public static function resolve_image( array $args = array() ): array {
		$post_id = $args['post_id'] ?? ( is_singular() ? get_the_ID() : 0 );

		/* 1. Custom social image */
		if ( $post_id ) {
			$custom = Settings::get_post_meta( (int) $post_id, 'social_image' );
			if ( $custom ) {
				return array(
					'url'    => $custom,
					'width'  => 1200,
					'height' => 630,
				);
			}
		}

		/* 2. Featured image */
		if ( $post_id && has_post_thumbnail( $post_id ) ) {
			$thumb_id = get_post_thumbnail_id( $post_id );
			$src      = wp_get_attachment_image_src( $thumb_id, 'large' );
			if ( $src ) {
				return array(
					'url'    => $src[0],
					'width'  => $src[1],
					'height' => $src[2],
					'alt'    => get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ),
				);
			}
		}

		/* 3. First content image */
		if ( $post_id ) {
			$post = get_post( $post_id );
			if ( $post && preg_match( '/<img[^>]+src=["\']([^"\']+)["\']/', $post->post_content, $m ) ) {
				return array(
					'url'    => $m[1],
					'width'  => 1200,
					'height' => 630,
				);
			}
		}

		/* 4. Apollo event banner meta */
		if ( $post_id ) {
			$banner = get_post_meta( $post_id, '_event_banner', true )
				?: get_post_meta( $post_id, '_dj_image', true )
				?: get_post_meta( $post_id, '_dj_banner', true );
			if ( $banner ) {
				if ( is_numeric( $banner ) ) {
					$src = wp_get_attachment_image_src( (int) $banner, 'large' );
					if ( $src ) {
						return array(
							'url'    => $src[0],
							'width'  => $src[1],
							'height' => $src[2],
						);
					}
				} else {
					return array(
						'url'    => $banner,
						'width'  => 1200,
						'height' => 630,
					);
				}
			}
		}

		/* 5. Default fallback */
		$default = Settings::get( 'default_og_image', 'https://assets.apollo.rio.br/img/thumb/thumb.jpg' );
		return array(
			'url'    => $default,
			'width'  => 1200,
			'height' => 630,
		);
	}

	/*
	═══════════════════════════════════════════════════════════════
		CANONICAL URL
		═══════════════════════════════════════════════════════════════ */

	/**
	 * Build canonical URL.
	 */
	public static function canonical_url(): string {
		if ( is_singular() ) {
			$id = get_the_ID();
			if ( $id ) {
				$custom = Settings::get_post_meta( $id, 'canonical' );
				if ( $custom ) {
					return esc_url( $custom );
				}
			}
			return esc_url( get_permalink() );
		}

		if ( is_front_page() ) {
			return esc_url( home_url( '/' ) );
		}

		if ( is_post_type_archive() ) {
			return esc_url( get_post_type_archive_link( get_query_var( 'post_type' ) ) );
		}

		if ( is_tax() || is_category() || is_tag() ) {
			$term = get_queried_object();
			if ( $term ) {
				return esc_url( get_term_link( $term ) );
			}
		}

		if ( is_author() ) {
			return esc_url( get_author_posts_url( get_queried_object_id() ) );
		}

		return esc_url( home_url( $_SERVER['REQUEST_URI'] ?? '/' ) );
	}

	/*
	═══════════════════════════════════════════════════════════════
		ROBOTS
		═══════════════════════════════════════════════════════════════ */

	/**
	 * Build robots meta content.
	 */
	public static function robots_content(): string {
		$directives = array( 'index', 'follow' );

		/* Post-level noindex */
		if ( is_singular() ) {
			$id = get_the_ID();
			if ( $id && Settings::get_post_meta( $id, 'noindex' ) ) {
				$directives[0] = 'noindex';
			}
			if ( $id && Settings::get_post_meta( $id, 'nofollow' ) ) {
				$directives[1] = 'nofollow';
			}
		}

		/* Global noindex rules */
		if ( is_search() && Settings::get( 'noindex_search' ) ) {
			$directives[0] = 'noindex';
		}

		if ( is_paged() && Settings::get( 'noindex_paginated' ) ) {
			$directives[0] = 'noindex';
		}

		if ( is_404() ) {
			$directives[0] = 'noindex';
		}

		if ( is_date() && Settings::get( 'noindex_date_archives' ) ) {
			$directives[0] = 'noindex';
		}

		if ( is_author() && Settings::get( 'noindex_author_archives' ) ) {
			$directives[0] = 'noindex';
		}

		if ( is_feed() && Settings::get( 'noindex_feeds' ) ) {
			$directives[0] = 'noindex';
		}

		$directives[] = 'max-snippet:-1';
		$directives[] = 'max-image-preview:large';
		$directives[] = 'max-video-preview:-1';

		return implode( ', ', $directives );
	}

	/*
	═══════════════════════════════════════════════════════════════
		OG TYPE
		═══════════════════════════════════════════════════════════════ */

	/**
	 * Determine og:type.
	 */
	private static function og_type(): string {
		if ( is_singular() ) {
			$type = get_post_type();
			if ( $type === 'event' ) {
				return 'article';
			}
			return 'article';
		}
		if ( is_author() ) {
			return 'profile';
		}
		return 'website';
	}

	/*
	═══════════════════════════════════════════════════════════════
		HEAD OUTPUT — Standard WP (wp_head hook)
		═══════════════════════════════════════════════════════════════ */

	/**
	 * Output all SEO meta tags in wp_head.
	 */
	public static function output_head(): void {
		echo "\n<!-- Apollo SEO v" . APOLLO_SEO_VERSION . " -->\n";
		self::print_tags();
		echo "<!-- /Apollo SEO -->\n\n";
	}

	/**
	 * Output tags only (for blank canvas via do_action).
	 */
	public static function output_head_tags_only(): void {
		self::print_tags();
	}

	/**
	 * Print all meta tags.
	 */
	private static function print_tags(): void {
		$title       = self::build_title();
		$description = self::build_description();
		$canonical   = self::canonical_url();
		$robots      = self::robots_content();
		$image       = self::resolve_image();
		$og_type     = self::og_type();
		$locale      = get_locale();
		$site_name   = Settings::get( 'site_title' ) ?: get_bloginfo( 'name' );

		/* OG title / description overrides */
		$og_title = $title;
		$og_desc  = $description;

		if ( is_singular() ) {
			$id = get_the_ID();
			if ( $id ) {
				$ot = Settings::get_post_meta( $id, 'og_title' );
				$od = Settings::get_post_meta( $id, 'og_description' );
				if ( $ot ) {
					$og_title = $ot;
				}
				if ( $od ) {
					$og_desc = $od;
				}
			}
		}

		if ( is_front_page() ) {
			$ot = Settings::get( 'homepage_og_title' );
			$od = Settings::get( 'homepage_og_desc' );
			if ( $ot ) {
				$og_title = $ot;
			}
			if ( $od ) {
				$og_desc = $od;
			}
		}

		/* ── Robots ── */
		printf( '<meta name="robots" content="%s">' . "\n", esc_attr( $robots ) );

		/* ── Canonical ── */
		if ( $canonical ) {
			printf( '<link rel="canonical" href="%s">' . "\n", $canonical );
		}

		/* ── Description ── */
		if ( $description ) {
			printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
		}

		/* ── Open Graph ── */
		if ( Settings::get( 'og_tags' ) ) {
			printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $og_type ) );
			printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( $locale ) );
			printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( $site_name ) );
			printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $og_title ) );

			if ( $og_desc ) {
				printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $og_desc ) );
			}

			if ( $canonical ) {
				printf( '<meta property="og:url" content="%s">' . "\n", $canonical );
			}

			if ( ! empty( $image['url'] ) ) {
				printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image['url'] ) );
				if ( ! empty( $image['width'] ) ) {
					printf( '<meta property="og:image:width" content="%d">' . "\n", $image['width'] );
				}
				if ( ! empty( $image['height'] ) ) {
					printf( '<meta property="og:image:height" content="%d">' . "\n", $image['height'] );
				}
				if ( ! empty( $image['alt'] ) ) {
					printf( '<meta property="og:image:alt" content="%s">' . "\n", esc_attr( $image['alt'] ) );
				}
				/* og:image:type — detect MIME from URL extension */
				$ext_map = array(
					'jpg'  => 'image/jpeg',
					'jpeg' => 'image/jpeg',
					'png'  => 'image/png',
					'gif'  => 'image/gif',
					'webp' => 'image/webp',
					'svg'  => 'image/svg+xml',
				);
				$ext     = strtolower( pathinfo( wp_parse_url( $image['url'], PHP_URL_PATH ) ?? '', PATHINFO_EXTENSION ) );
				if ( isset( $ext_map[ $ext ] ) ) {
					printf( '<meta property="og:image:type" content="%s">' . "\n", esc_attr( $ext_map[ $ext ] ) );
				}
			}

			/* Article dates */
			if ( is_singular() ) {
				printf( '<meta property="article:published_time" content="%s">' . "\n", esc_attr( get_the_date( 'c' ) ) );
				printf( '<meta property="article:modified_time" content="%s">' . "\n", esc_attr( get_the_modified_date( 'c' ) ) );

				/* article:author */
				if ( Settings::get( 'og_article_author' ) ) {
					$author_id  = (int) get_the_author_meta( 'ID' );
					$author_url = get_author_posts_url( $author_id );
					if ( $author_url ) {
						printf( '<meta property="article:author" content="%s">' . "\n", esc_url( $author_url ) );
					}
				}

				/* article:publisher */
				$publisher = Settings::get( 'og_article_publisher' );
				if ( ! $publisher ) {
					$publisher = Settings::get( 'social_facebook' );
				}
				if ( $publisher ) {
					printf( '<meta property="article:publisher" content="%s">' . "\n", esc_url( $publisher ) );
				}
			}

			/* Facebook App ID */
			$fb_app = Settings::get( 'facebook_app_id' );
			if ( $fb_app ) {
				printf( '<meta property="fb:app_id" content="%s">' . "\n", esc_attr( $fb_app ) );
			}
		}

		/* ── Twitter Cards ── */
		if ( Settings::get( 'twitter_tags' ) ) {
			printf( '<meta name="twitter:card" content="%s">' . "\n", esc_attr( Settings::get( 'twitter_card_type', 'summary_large_image' ) ) );

			$tw_site = Settings::get( 'twitter_site' );
			if ( $tw_site ) {
				printf( '<meta name="twitter:site" content="%s">' . "\n", esc_attr( $tw_site ) );
			}

			printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $og_title ) );

			if ( $og_desc ) {
				printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $og_desc ) );
			}

			if ( ! empty( $image['url'] ) ) {
				printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image['url'] ) );
				if ( ! empty( $image['alt'] ) ) {
					printf( '<meta name="twitter:image:alt" content="%s">' . "\n", esc_attr( $image['alt'] ) );
				}
			}
		}

		/* ── Webmaster Verification ── */
		$google = Settings::get( 'google_verification' );
		if ( $google ) {
			printf( '<meta name="google-site-verification" content="%s">' . "\n", esc_attr( $google ) );
		}

		$bing = Settings::get( 'bing_verification' );
		if ( $bing ) {
			printf( '<meta name="msvalidate.01" content="%s">' . "\n", esc_attr( $bing ) );
		}

		$pinterest = Settings::get( 'pinterest_verification' );
		if ( $pinterest ) {
			printf( '<meta name="p:domain_verify" content="%s">' . "\n", esc_attr( $pinterest ) );
		}

		$yandex = Settings::get( 'yandex_verification' );
		if ( $yandex ) {
			printf( '<meta name="yandex-verification" content="%s">' . "\n", esc_attr( $yandex ) );
		}

		/* ── Schema.org JSON-LD ── */
		if ( Settings::get( 'schema_enabled' ) ) {
			$schema = Schema::build();
			if ( $schema ) {
				echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ) . '</script>' . "\n";
			}
		}
	}

	/*
	═══════════════════════════════════════════════════════════════
		BLANK CANVAS HELPER
		═══════════════════════════════════════════════════════════════ */

	/**
	 * Generate meta tags as a string for blank canvas templates.
	 * Usage in template: <?php echo \Apollo\SEO\Meta::head_string( $args ); ?>
	 */
	public static function head_string( array $args = array() ): string {
		ob_start();
		self::print_tags();
		return ob_get_clean();
	}

	/**
	 * Get title for use in <title> tag in blank canvas.
	 * Usage: <title><?php echo \Apollo\SEO\Meta::title_for( $args ); ?></title>
	 */
	public static function title_for( array $args = array() ): string {
		return esc_html( self::build_title( $args ) );
	}
}
