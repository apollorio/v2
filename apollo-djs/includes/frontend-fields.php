<?php
/**
 * Apollo DJs — Frontend Editor Field Definitions
 *
 * Registers DJ fields with the shared frontend editing system
 * (apollo-templates/FrontendEditor).
 *
 * - Registers 'dj' as an editable CPT
 * - Defines all DJ meta fields with types, labels, icons, sections
 * - Configures the editor layout (hero with cover + avatar)
 *
 * URL: /editar/dj/{post_id}/
 *
 * @package Apollo\DJs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
═══════════════════════════════════════════════════════════════════════════
	REGISTER DJ AS EDITABLE CPT
	═══════════════════════════════════════════════════════════════════════════ */

add_filter(
	'apollo_editable_post_types',
	function ( array $types ): array {
		$types[] = 'dj';
		return $types;
	}
);

/*
═══════════════════════════════════════════════════════════════════════════
	EDITOR CONFIGURATION
	═══════════════════════════════════════════════════════════════════════════ */

add_filter(
	'apollo_editor_config_dj',
	function ( array $config ): array {
		$config['page_title']   = __( 'Editar DJ', 'apollo-djs' );
		$config['hero_enabled'] = true;
		$config['cover_field']  = '_dj_banner';
		$config['avatar_field'] = '_dj_image';
		$config['sections']     = array( 'hero', 'main', 'links', 'settings' );

		$config['section_labels'] = array(
			'hero'     => __( 'Destaque', 'apollo-djs' ),
			'main'     => __( 'Informações', 'apollo-djs' ),
			'links'    => __( 'Links & Redes Sociais', 'apollo-djs' ),
			'settings' => __( 'Configurações', 'apollo-djs' ),
		);

		$config['section_icons'] = array(
			'hero'     => 'ri-disc-line',
			'main'     => 'ri-information-line',
			'links'    => 'ri-links-line',
			'settings' => 'ri-settings-3-line',
		);

		$config['save_label'] = __( 'Salvar DJ', 'apollo-djs' );

		return $config;
	}
);

/*
═══════════════════════════════════════════════════════════════════════════
	FIELD DEFINITIONS
	═══════════════════════════════════════════════════════════════════════════ */

add_filter(
	'apollo_frontend_fields_dj',
	function ( array $fields ): array {

		// ─── Bio (main section) ─────────────────────────────────────────────
		$fields[] = array(
			'name'        => '_dj_bio_short',
			'type'        => 'textarea',
			'label'       => __( 'Bio curta', 'apollo-djs' ),
			'icon'        => 'ri-quill-pen-line',
			'placeholder' => __( 'Escreva uma bio curta sobre o DJ...', 'apollo-djs' ),
			'required'    => false,
			'maxlength'   => 280,
			'section'     => 'main',
			'rows'        => 3,
			'description' => __( 'Aparece no card e no perfil. Máximo 280 caracteres.', 'apollo-djs' ),
		);

		$fields[] = array(
			'name'        => '_dj_name',
			'type'        => 'text',
			'label'       => __( 'Nome artístico', 'apollo-djs' ),
			'icon'        => 'ri-user-star-line',
			'placeholder' => __( 'Nome artístico (se diferente do título)', 'apollo-djs' ),
			'section'     => 'main',
		);

		$fields[] = array(
			'name'        => '_dj_bio',
			'type'        => 'textarea',
			'label'       => __( 'Bio completa', 'apollo-djs' ),
			'icon'        => 'ri-file-text-line',
			'placeholder' => __( 'Escreva a bio completa do DJ...', 'apollo-djs' ),
			'section'     => 'main',
			'rows'        => 8,
		);

		// ─── Projetos originais (main section) ─────────────────────────────
		$fields[] = array(
			'name'    => '_dj_original_project_1',
			'type'    => 'text',
			'label'   => __( 'Projeto original 1', 'apollo-djs' ),
			'icon'    => 'ri-star-line',
			'section' => 'main',
		);

		$fields[] = array(
			'name'    => '_dj_original_project_2',
			'type'    => 'text',
			'label'   => __( 'Projeto original 2', 'apollo-djs' ),
			'icon'    => 'ri-star-line',
			'section' => 'main',
		);

		$fields[] = array(
			'name'    => '_dj_original_project_3',
			'type'    => 'text',
			'label'   => __( 'Projeto original 3', 'apollo-djs' ),
			'icon'    => 'ri-star-line',
			'section' => 'main',
		);

		// ─── Sounds / Taxonomy ──────────────────────────────────────────────
		$fields[] = array(
			'name'        => '_dj_sounds',
			'type'        => 'taxonomy',
			'label'       => __( 'Sons / Estilos', 'apollo-djs' ),
			'icon'        => 'ri-music-2-line',
			'section'     => 'main',
			'taxonomy'    => 'sound',
			'tax_format'  => 'checkbox',
			'description' => __( 'Selecione os estilos musicais deste DJ.', 'apollo-djs' ),
		);

		// ─── Links section ──────────────────────────────────────────────────
		$fields[] = array(
			'name'        => '_dj_website',
			'type'        => 'url',
			'label'       => __( 'Website', 'apollo-djs' ),
			'icon'        => 'ri-global-line',
			'placeholder' => 'https://site-do-dj.com',
			'section'     => 'links',
		);

		$fields[] = array(
			'name'        => '_dj_instagram',
			'type'        => 'url',
			'label'       => __( 'Instagram', 'apollo-djs' ),
			'icon'        => 'ri-instagram-line',
			'placeholder' => 'https://instagram.com/djname',
			'section'     => 'links',
		);

		$fields[] = array(
			'name'        => '_dj_soundcloud',
			'type'        => 'url',
			'label'       => __( 'SoundCloud', 'apollo-djs' ),
			'icon'        => 'ri-soundcloud-line',
			'placeholder' => 'https://soundcloud.com/djname',
			'section'     => 'links',
		);

		$fields[] = array(
			'name'        => '_dj_spotify',
			'type'        => 'url',
			'label'       => __( 'Spotify', 'apollo-djs' ),
			'icon'        => 'ri-spotify-line',
			'placeholder' => 'https://open.spotify.com/artist/...',
			'section'     => 'links',
		);

		$fields[] = array(
			'name'        => '_dj_youtube',
			'type'        => 'url',
			'label'       => __( 'YouTube', 'apollo-djs' ),
			'icon'        => 'ri-youtube-line',
			'placeholder' => 'https://youtube.com/@djname',
			'section'     => 'links',
		);

		$fields[] = array(
			'name'        => '_dj_mixcloud',
			'type'        => 'url',
			'label'       => __( 'Mixcloud', 'apollo-djs' ),
			'icon'        => 'ri-disc-line',
			'placeholder' => 'https://mixcloud.com/djname',
			'section'     => 'links',
		);

		$fields[] = array(
			'name'        => '_dj_facebook',
			'type'        => 'url',
			'label'       => __( 'Facebook', 'apollo-djs' ),
			'icon'        => 'ri-facebook-circle-line',
			'placeholder' => 'https://facebook.com/djname',
			'section'     => 'links',
		);

		$fields[] = array(
			'name'        => '_dj_twitter',
			'type'        => 'url',
			'label'       => __( 'Twitter / X', 'apollo-djs' ),
			'icon'        => 'ri-twitter-x-line',
			'placeholder' => 'https://x.com/djname',
			'section'     => 'links',
		);

		$fields[] = array(
			'name'        => '_dj_tiktok',
			'type'        => 'url',
			'label'       => __( 'TikTok', 'apollo-djs' ),
			'icon'        => 'ri-tiktok-line',
			'placeholder' => 'https://tiktok.com/@djname',
			'section'     => 'links',
		);

		$fields[] = array(
			'name'        => '_dj_bandcamp',
			'type'        => 'url',
			'label'       => __( 'Bandcamp', 'apollo-djs' ),
			'icon'        => 'ri-album-line',
			'placeholder' => 'https://djname.bandcamp.com',
			'section'     => 'links',
		);

		$fields[] = array(
			'name'        => '_dj_beatport',
			'type'        => 'url',
			'label'       => __( 'Beatport', 'apollo-djs' ),
			'icon'        => 'ri-vip-crown-line',
			'placeholder' => 'https://beatport.com/artist/djname',
			'section'     => 'links',
		);

		$fields[] = array(
			'name'        => '_dj_resident_advisor',
			'type'        => 'url',
			'label'       => __( 'Resident Advisor', 'apollo-djs' ),
			'icon'        => 'ri-radio-line',
			'placeholder' => 'https://ra.co/dj/djname',
			'section'     => 'links',
		);

		$fields[] = array(
			'name'        => '_dj_set_url',
			'type'        => 'url',
			'label'       => __( 'Set em destaque (SoundCloud)', 'apollo-djs' ),
			'icon'        => 'ri-play-circle-line',
			'placeholder' => 'https://soundcloud.com/djname/set-name',
			'section'     => 'links',
		);

		$fields[] = array(
			'name'        => '_dj_media_kit_url',
			'type'        => 'url',
			'label'       => __( 'Media Kit URL', 'apollo-djs' ),
			'icon'        => 'ri-clipboard-line',
			'placeholder' => 'https://link-para-media-kit.com',
			'section'     => 'links',
		);

		$fields[] = array(
			'name'        => '_dj_rider_url',
			'type'        => 'url',
			'label'       => __( 'Rider técnico', 'apollo-djs' ),
			'icon'        => 'ri-clipboard-fill',
			'placeholder' => 'https://link-para-rider.com',
			'section'     => 'links',
		);

		$fields[] = array(
			'name'        => '_dj_mix_url',
			'type'        => 'url',
			'label'       => __( 'Mix / Playlist URL', 'apollo-djs' ),
			'icon'        => 'ri-play-list-2-line',
			'placeholder' => 'https://link-para-mix.com',
			'section'     => 'links',
		);

		// ─── Settings section ───────────────────────────────────────────────
		$fields[] = array(
			'name'        => '_dj_verified',
			'type'        => 'checkbox',
			'label'       => __( 'DJ Verificado', 'apollo-djs' ),
			'icon'        => 'ri-verified-badge-line',
			'section'     => 'settings',
			'readonly'    => true, // Only admin can change
			'description' => __( 'Status de verificação (gerenciado pela equipe).', 'apollo-djs' ),
		);

		$fields[] = array(
			'name'    => '_dj_user_id',
			'type'    => 'hidden',
			'section' => 'settings',
		);

		return $fields;
	}
);

/*
═══════════════════════════════════════════════════════════════════════════
	PERMISSION EXTENSION — DJ linked user can edit
	═══════════════════════════════════════════════════════════════════════════ */

add_filter(
	'apollo_editor_can_edit_dj',
	function ( bool $can, int $post_id, int $user_id ): bool {
		if ( $can ) {
			return true;
		}

		// Check if user is linked to this DJ via _dj_user_id
		$linked = (int) get_post_meta( $post_id, '_dj_user_id', true );
		return $linked === $user_id;
	},
	10,
	3
);
