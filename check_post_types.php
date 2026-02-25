<?php
require_once 'C:/Users/rafae/Local Sites/apollo/app/public/wp-load.php';
$post_types = get_post_types(array('public' => true), 'names');
echo 'Public post types: ' . implode(', ', $post_types) . PHP_EOL;
$all_types = get_post_types(array(), 'names');
echo 'All post types: ' . implode(', ', $all_types) . PHP_EOL;
