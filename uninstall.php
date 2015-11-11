<?php
// This script runs automatically when the InterVarsity plugin is uninstalled

// Prevent this file from being run directly
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Permanently delete all small groups
$small_groups = get_posts( array(
	'post_type'      => 'iv_small_group',
	'post_status'    => 'any',
	'posts_per_page' => -1
) );
foreach ( $small_groups as $small_group ) {
	wp_delete_post( $small_group->ID, true );
}

// Permanently delete all associations between sliders and pages
$pages = get_pages( array(
	'post_status'    => 'any',
	'posts_per_page' => -1
) );
foreach ( $pages as $page ) {
	delete_post_meta( $page->ID, '_iv_slider_id' );
}
