<?php
/**
 * Template Name: Full Width Template
 * Template Post Type: post, page
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 */
get_header();
?>

<?php
    /*
    * Content section for full width layout
    * Every section on this page will managed from Page Builder Plugins
    */
    the_content(); 
?>

<?php
get_footer();