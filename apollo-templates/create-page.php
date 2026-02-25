<?php

/**
 * One-time script to create /classificados page
 * Run via browser: http://localhost:10004/wp-content/plugins/apollo-templates/create-page.php
 */

require_once '../../../wp-load.php';

// Create classificados page if it doesn't exist
$page = get_page_by_path( 'classificados' );

if ( ! $page ) {
	$page_id = wp_insert_post(
		array(
			'post_title'     => 'Classificados',
			'post_name'      => 'classificados',
			'post_content'   => '',
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		)
	);

	if ( $page_id && ! is_wp_error( $page_id ) ) {
		update_post_meta( $page_id, '_wp_page_template', 'templates/page-classificados.php' );
		echo "✅ Página 'Classificados' criada com ID: $page_id<br>";
	} else {
		echo '❌ Erro ao criar página<br>';
		if ( is_wp_error( $page_id ) ) {
			echo 'Erro: ' . $page_id->get_error_message() . '<br>';
		}
	}
} else {
	echo "ℹ️ Página 'Classificados' já existe (ID: {$page->ID})<br>";

	// Update template just in case
	update_post_meta( $page->ID, '_wp_page_template', 'templates/page-classificados.php' );
	echo '✅ Template atualizado<br>';
}

// Flush rewrite rules
flush_rewrite_rules();
echo '✅ Rewrite rules flushed<br>';

echo "<br><strong>Teste a página:</strong> <a href='" . home_url( '/classificados' ) . "' target='_blank'>" . home_url( '/classificados' ) . '</a>';
echo '<br><br><em>Você pode deletar este arquivo agora (create-page.php)</em>';
