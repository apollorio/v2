<?php

/**
 * Apollo Ecosystem — CPT Definitions
 *
 * All 9 Custom Post Types defined centrally.
 * Used by CPTRegistry for fallback registration.
 *
 * Structure: slug => [ owner, rewrite, archive, rest_base, public, supports, labels, menu_icon ]
 *
 * @package Apollo\Core
 * @since   6.1.0
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

return array(

    /*
	═══════════════════════════════════════════════════════════════════
	 * EVENT — Owner: apollo-events
	 * ═══════════════════════════════════════════════════════════════════ */
    'event'        => array(
        'owner'       => 'apollo-events',
        'slug'        => 'event',
        'rewrite'     => 'evento',
        'archive'     => 'eventos',
        'rest_base'   => 'events',
        'public'      => true,
        'has_archive' => true,
        'supports'    => array('title', 'editor', 'thumbnail', 'author'),
        'labels'      => array(
            'name'               => 'Eventos',
            'singular_name'      => 'Evento',
            'add_new'            => 'Adicionar Novo',
            'add_new_item'       => 'Adicionar Novo Evento',
            'edit_item'          => 'Editar Evento',
            'new_item'           => 'Novo Evento',
            'view_item'          => 'Ver Evento',
            'search_items'       => 'Buscar Eventos',
            'not_found'          => 'Nenhum evento encontrado',
            'not_found_in_trash' => 'Nenhum evento na lixeira',
            'menu_name'          => 'Eventos',
        ),
        'menu_icon'   => 'dashicons-calendar-alt',
    ),

    /*
	═══════════════════════════════════════════════════════════════════
	 * DJ — Owner: apollo-djs
	 * ═══════════════════════════════════════════════════════════════════ */
    'dj'           => array(
        'owner'       => 'apollo-djs',
        'slug'        => 'dj',
        'rewrite'     => 'dj',
        'archive'     => 'djs',
        'rest_base'   => 'djs',
        'public'      => true,
        'has_archive' => true,
        'supports'    => array('title', 'editor', 'thumbnail', 'author'),
        'labels'      => array(
            'name'               => 'DJs',
            'singular_name'      => 'DJ',
            'add_new'            => 'Adicionar Novo',
            'add_new_item'       => 'Adicionar Novo DJ',
            'edit_item'          => 'Editar DJ',
            'new_item'           => 'Novo DJ',
            'view_item'          => 'Ver DJ',
            'search_items'       => 'Buscar DJs',
            'not_found'          => 'Nenhum DJ encontrado',
            'not_found_in_trash' => 'Nenhum DJ na lixeira',
            'menu_name'          => 'DJs',
        ),
        'menu_icon'   => 'dashicons-format-audio',
    ),

    /*
	═══════════════════════════════════════════════════════════════════
	 * LOCAL — Owner: apollo-loc  (URL: /local/{slug})
	 * ═════════════════════════════════════════════════════════════════ */
    'local'        => array(
        'owner'       => 'apollo-loc',
        'slug'        => 'local',
        'rewrite'     => 'local',
        'archive'     => 'local',
        'rest_base'   => 'local',
        'public'      => true,
        'has_archive' => true,
        'supports'    => array('title', 'editor', 'thumbnail'),
        'labels'      => array(
            'name'               => 'Locais',
            'singular_name'      => 'Local',
            'add_new'            => 'Adicionar Novo',
            'add_new_item'       => 'Adicionar Novo Local',
            'edit_item'          => 'Editar Local',
            'new_item'           => 'Novo Local',
            'view_item'          => 'Ver Local',
            'search_items'       => 'Buscar Locais',
            'not_found'          => 'Nenhum local encontrado',
            'not_found_in_trash' => 'Nenhum local na lixeira',
            'menu_name'          => 'Locais',
        ),
        'menu_icon'   => 'dashicons-location',
    ),

    /*
	═══════════════════════════════════════════════════════════════════
	 * CLASSIFIED — Owner: apollo-adverts
	 * ═══════════════════════════════════════════════════════════════════ */
    'classified'   => array(
        'owner'       => 'apollo-adverts',
        'slug'        => 'classified',
        'rewrite'     => 'anuncio',
        'archive'     => 'anuncios',
        'rest_base'   => 'classifieds',
        'public'      => true,
        'has_archive' => true,
        'supports'    => array('title', 'editor', 'thumbnail', 'author'),
        'labels'      => array(
            'name'               => 'Anúncios',
            'singular_name'      => 'Anúncio',
            'add_new'            => 'Adicionar Novo',
            'add_new_item'       => 'Adicionar Novo Anúncio',
            'edit_item'          => 'Editar Anúncio',
            'new_item'           => 'Novo Anúncio',
            'view_item'          => 'Ver Anúncio',
            'search_items'       => 'Buscar Anúncios',
            'not_found'          => 'Nenhum anúncio encontrado',
            'not_found_in_trash' => 'Nenhum anúncio na lixeira',
            'menu_name'          => 'Classificados',
        ),
        'menu_icon'   => 'dashicons-megaphone',
    ),

    /*
	═══════════════════════════════════════════════════════════════════
	 * SUPPLIER — Owner: apollo-suppliers  (industry-only)
	 * ═══════════════════════════════════════════════════════════════════ */
    'supplier'     => array(
        'owner'       => 'apollo-suppliers',
        'slug'        => 'supplier',
        'rewrite'     => 'fornecedor',
        'archive'     => 'fornecedores',
        'rest_base'   => 'suppliers',
        'public'      => false,
        'has_archive' => false,
        'supports'    => array('title', 'editor', 'thumbnail'),
        'labels'      => array(
            'name'               => 'Fornecedores',
            'singular_name'      => 'Fornecedor',
            'add_new'            => 'Adicionar Novo',
            'add_new_item'       => 'Adicionar Novo Fornecedor',
            'edit_item'          => 'Editar Fornecedor',
            'new_item'           => 'Novo Fornecedor',
            'view_item'          => 'Ver Fornecedor',
            'search_items'       => 'Buscar Fornecedores',
            'not_found'          => 'Nenhum fornecedor encontrado',
            'not_found_in_trash' => 'Nenhum fornecedor na lixeira',
            'menu_name'          => 'Fornecedores',
        ),
        'menu_icon'   => 'dashicons-store',
    ),

    /*
	═══════════════════════════════════════════════════════════════════
	 * DOC — Owner: apollo-docs
	 * ═══════════════════════════════════════════════════════════════════ */
    'doc'          => array(
        'owner'       => 'apollo-docs',
        'slug'        => 'doc',
        'rewrite'     => 'documento',
        'archive'     => false,
        'rest_base'   => 'docs',
        'public'      => false,
        'has_archive' => false,
        'supports'    => array('title', 'editor', 'author'),
        'labels'      => array(
            'name'               => 'Documentos',
            'singular_name'      => 'Documento',
            'add_new'            => 'Adicionar Novo',
            'add_new_item'       => 'Adicionar Novo Documento',
            'edit_item'          => 'Editar Documento',
            'new_item'           => 'Novo Documento',
            'view_item'          => 'Ver Documento',
            'search_items'       => 'Buscar Documentos',
            'not_found'          => 'Nenhum documento encontrado',
            'not_found_in_trash' => 'Nenhum documento na lixeira',
            'menu_name'          => 'Documentos',
        ),
        'menu_icon'   => 'dashicons-media-document',
    ),

    /*
	═══════════════════════════════════════════════════════════════════
	 * EMAIL_APRIO — Owner: apollo-email  (aprio = ApolloRIO)
	 * ═══════════════════════════════════════════════════════════════════ */
    'email_aprio'  => array(
        'owner'       => 'apollo-email',
        'slug'        => 'email_aprio',
        'rewrite'     => false,
        'archive'     => false,
        'rest_base'   => 'email-templates',
        'public'      => false,
        'has_archive' => false,
        'supports'    => array('title', 'editor'),
        'labels'      => array(
            'name'               => 'Email Templates',
            'singular_name'      => 'Email Template',
            'add_new'            => 'Adicionar Novo',
            'add_new_item'       => 'Adicionar Novo Template',
            'edit_item'          => 'Editar Template',
            'new_item'           => 'Novo Template',
            'view_item'          => 'Ver Template',
            'search_items'       => 'Buscar Templates',
            'not_found'          => 'Nenhum template encontrado',
            'not_found_in_trash' => 'Nenhum template na lixeira',
            'menu_name'          => 'Email Templates',
        ),
        'menu_icon'   => 'dashicons-email-alt',
    ),

    /*
	═══════════════════════════════════════════════════════════════════
	 * HUB — Owner: apollo-hub  (Linktree-style /hub/{username})
	 * ═══════════════════════════════════════════════════════════════════ */
    'hub'          => array(
        'owner'       => 'apollo-hub',
        'slug'        => 'hub',
        'rewrite'     => 'hub',
        'archive'     => false,
        'rest_base'   => 'hubs',
        'public'      => true,
        'has_archive' => false,
        'supports'    => array('title', 'author'),
        'labels'      => array(
            'name'               => 'Hubs',
            'singular_name'      => 'Hub',
            'add_new'            => 'Adicionar Novo',
            'add_new_item'       => 'Adicionar Novo Hub',
            'edit_item'          => 'Editar Hub',
            'new_item'           => 'Novo Hub',
            'view_item'          => 'Ver Hub',
            'search_items'       => 'Buscar Hubs',
            'not_found'          => 'Nenhum hub encontrado',
            'not_found_in_trash' => 'Nenhum hub na lixeira',
            'menu_name'          => 'Hubs',
        ),
        'menu_icon'   => 'dashicons-admin-links',
    ),

    /*
	═══════════════════════════════════════════════════════════════════
	 * APOLLO_SHEET — Owner: apollo-sheets
	 * ═══════════════════════════════════════════════════════════════════ */
    'apollo_sheet' => array(
        'owner'       => 'apollo-sheets',
        'slug'        => 'apollo_sheet',
        'rewrite'     => false,
        'archive'     => false,
        'rest_base'   => 'sheets',
        'public'      => false,
        'has_archive' => false,
        'supports'    => array('title', 'editor', 'excerpt', 'revisions', 'author'),
        'labels'      => array(
            'name'               => 'Sheets',
            'singular_name'      => 'Sheet',
            'add_new'            => 'Adicionar Nova',
            'add_new_item'       => 'Adicionar Nova Sheet',
            'edit_item'          => 'Editar Sheet',
            'new_item'           => 'Nova Sheet',
            'view_item'          => 'Ver Sheet',
            'search_items'       => 'Buscar Sheets',
            'not_found'          => 'Nenhuma sheet encontrada',
            'not_found_in_trash' => 'Nenhuma sheet na lixeira',
            'menu_name'          => 'Sheets',
        ),
        'menu_icon'   => 'dashicons-editor-table',
    ),
);
