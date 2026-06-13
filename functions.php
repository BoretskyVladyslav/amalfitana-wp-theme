<?php
/**
 * Amalfitana Theme functions and definitions.
 *
 * @package Amalfitana_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Google Fonts on the frontend and in the block editor.
 */
function amalfitana_enqueue_google_fonts() {
	$fonts_url = 'https://fonts.googleapis.com/css2?family=Imperial+Script&family=Instrument+Serif:ital@0;1&family=Inter:wght@400;500;600;700&display=swap';

	wp_enqueue_style(
		'amalfitana-google-fonts',
		$fonts_url,
		array(),
		null
	);
}
add_action( 'wp_enqueue_scripts', 'amalfitana_enqueue_google_fonts' );
add_action( 'enqueue_block_editor_assets', 'amalfitana_enqueue_google_fonts' );

/**
 * Enqueue global UI kit styles.
 */
function amalfitana_enqueue_theme_styles() {
	wp_enqueue_style(
		'amalfitana-buttons',
		get_template_directory_uri() . '/assets/css/buttons.css',
		array( 'amalfitana-google-fonts' ),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'amalfitana-faq',
		get_template_directory_uri() . '/assets/css/faq.css',
		array( 'amalfitana-google-fonts' ),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'amalfitana-subscribe',
		get_template_directory_uri() . '/assets/css/subscribe.css',
		array( 'amalfitana-google-fonts', 'amalfitana-buttons' ),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'amalfitana-footer',
		get_template_directory_uri() . '/assets/css/footer.css',
		array( 'amalfitana-google-fonts' ),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'amalfitana_enqueue_theme_styles' );
add_action( 'enqueue_block_editor_assets', 'amalfitana_enqueue_theme_styles' );

/**
 * Enqueue theme scripts.
 */
function amalfitana_enqueue_theme_scripts() {
	wp_enqueue_script(
		'amalfitana-faq',
		get_template_directory_uri() . '/assets/js/faq.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);

	wp_enqueue_script(
		'amalfitana-subscribe',
		get_template_directory_uri() . '/assets/js/subscribe.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'amalfitana_enqueue_theme_scripts' );
