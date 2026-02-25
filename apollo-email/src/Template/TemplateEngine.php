<?php

/**
 * Template Engine — renders email templates with merge tags and Apollo branding.
 *
 * @package Apollo\Email\Template
 * @since   1.0.0
 */

declare(strict_types=1);

namespace Apollo\Email\Template;

use Apollo\Email\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TemplateEngine {


	/** @var string Path to email templates directory */
	private string $templates_path;

	public function __construct() {
		$this->templates_path = APOLLO_EMAIL_PATH . 'templates/emails/';
	}

	/**
	 * Render an email template with merge tags.
	 *
	 * @param string $template_slug Template slug (e.g., 'welcome', 'password-reset').
	 * @param array  $data          Merge tag data.
	 * @return string Rendered HTML.
	 */
	public function render( string $template_slug, array $data = array() ): string {
		/**
		 * Filter template data before rendering.
		 *
		 * @param array  $data          Template data.
		 * @param string $template_slug Template slug.
		 */
		$data = apply_filters( 'apollo/email/template_data', $data, $template_slug );

		// Add global data
		$data = array_merge(
			array(
				'site_name'       => get_bloginfo( 'name' ),
				'site_url'        => home_url( '/' ),
				'brand_color'     => Plugin::setting( 'brand_color', '#6C3BF5' ),
				'brand_logo'      => Plugin::setting( 'brand_logo', '' ),
				'footer_text'     => Plugin::setting( 'footer_text', '' ),
				'footer_address'  => Plugin::setting( 'footer_address', '' ),
				'current_year'    => gmdate( 'Y' ),
				'unsubscribe_url' => '',
				'preferences_url' => home_url( '/?apollo_email_prefs=1' ),
			),
			$data
		);

		// Try to get CPT template content first
		$cpt_content = $this->getFromCPT( $template_slug );

		// Get content block from PHP template file
		$content_html = $this->renderContentBlock( $template_slug, $data );

		// If CPT has custom content, use that instead
		if ( $cpt_content ) {
			$content_html = $this->replaceMergeTags( $cpt_content, $data );
		}

		// Wrap in base template
		$base_html = $this->renderBase( $data, $content_html );

		// Inline CSS for email client compatibility
		return StyleInliner::inline( $base_html );
	}

	/**
	 * Render a subject line with merge tags.
	 *
	 * @param string $template_slug Template slug.
	 * @param array  $data          Merge tag data.
	 * @return string Rendered subject.
	 */
	public function renderSubject( string $template_slug, array $data = array() ): string {
		// Try CPT custom subject first
		$template = $this->getCPTTemplate( $template_slug );
		if ( $template ) {
			$subject = get_post_meta( $template->ID, '_email_subject', true );
			if ( $subject ) {
				return $this->replaceMergeTags( $subject, $data );
			}
		}

		// Fallback — return data subject or empty
		return $data['subject'] ?? '';
	}

	/**
	 * Get available templates from CPT.
	 *
	 * @return array List of template objects.
	 */
	public function getTemplates(): array {
		$posts = get_posts(
			array(
				'post_type'   => 'email_aprio',
				'post_status' => 'publish',
				'numberposts' => 100,
				'orderby'     => 'title',
				'order'       => 'ASC',
			)
		);

		return array_map(
			function ( $post ) {
				return array(
					'id'        => $post->ID,
					'slug'      => $post->post_name,
					'title'     => $post->post_title,
					'subject'   => get_post_meta( $post->ID, '_email_subject', true ),
					'type'      => get_post_meta( $post->ID, '_email_type', true ),
					'variables' => get_post_meta( $post->ID, '_email_variables', true ),
					'content'   => $post->post_content,
					'modified'  => $post->post_modified,
				);
			},
			$posts
		);
	}

	/**
	 * Get template object by slug.
	 */
	public function getTemplate( string $slug ): ?array {
		$template = $this->getCPTTemplate( $slug );
		if ( ! $template ) {
			return null;
		}

		return array(
			'id'        => $template->ID,
			'slug'      => $template->post_name,
			'title'     => $template->post_title,
			'subject'   => get_post_meta( $template->ID, '_email_subject', true ),
			'type'      => get_post_meta( $template->ID, '_email_type', true ),
			'variables' => get_post_meta( $template->ID, '_email_variables', true ),
			'content'   => $template->post_content,
			'modified'  => $template->post_modified,
		);
	}

	/**
	 * Replace merge tags in content.
	 *
	 * @param string $content Content with {{tags}}.
	 * @param array  $data    Key-value replacements.
	 * @return string Processed content.
	 */
	public function replaceMergeTags( string $content, array $data ): string {
		return preg_replace_callback(
			'/\{\{(\w+)\}\}/',
			function ( $matches ) use ( $data ) {
				$key = $matches[1];
				if ( isset( $data[ $key ] ) ) {
					if ( is_array( $data[ $key ] ) ) {
						return wp_json_encode( $data[ $key ] );
					}
					return (string) $data[ $key ];
				}
				return $matches[0]; // Keep unreplaced tags
			},
			$content
		) ?? $content;
	}

	/**
	 * Get content from CPT post.
	 */
	private function getFromCPT( string $slug ): ?string {
		$template = $this->getCPTTemplate( $slug );
		if ( $template && ! empty( $template->post_content ) ) {
			return $template->post_content;
		}
		return null;
	}

	/**
	 * Get CPT post by slug.
	 */
	private function getCPTTemplate( string $slug ): ?\WP_Post {
		$posts = get_posts(
			array(
				'post_type'   => 'email_aprio',
				'name'        => $slug,
				'post_status' => 'publish',
				'numberposts' => 1,
			)
		);

		return $posts[0] ?? null;
	}

	/**
	 * Render content block from PHP template file.
	 */
	private function renderContentBlock( string $template_slug, array $data ): string {
		$file = $this->templates_path . $template_slug . '.php';
		if ( ! file_exists( $file ) ) {
			// Fallback to notification template
			$file = $this->templates_path . 'notification.php';
		}

		if ( ! file_exists( $file ) ) {
			return '<p>' . esc_html( $data['message'] ?? '' ) . '</p>';
		}

		// Extract data for template scope (EXTR_SKIP prevents overwrites)
		extract( $data, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

		ob_start();
		include $file;
		return ob_get_clean();
	}

	/**
	 * Render the base email wrapper template.
	 */
	private function renderBase( array $data, string $content ): string {
		$file = $this->templates_path . 'base.php';
		if ( ! file_exists( $file ) ) {
			return $content;
		}

		extract( $data, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		$email_content = $content;

		ob_start();
		include $file;
		return ob_get_clean();
	}
}
