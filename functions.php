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
 * Create the Tours page once if it does not exist yet.
 */
function amalfitana_maybe_create_tours_page() {
	if ( null !== get_page_by_path( 'tours' ) ) {
		return;
	}

	wp_insert_post(
		array(
			'post_title'  => 'Tours',
			'post_name'   => 'tours',
			'post_status' => 'publish',
			'post_type'   => 'page',
		)
	);
}
add_action( 'init', 'amalfitana_maybe_create_tours_page' );

/**
 * Return the canonical URL for the Tours page.
 */
function amalfitana_get_tours_page_url() {
	return esc_url( home_url( '/tours/' ) );
}

/**
 * Replace template placeholders in HTML blocks.
 */
function amalfitana_render_template_placeholders( $block_content, $block ) {
	if ( empty( $block['blockName'] ) || 'core/html' !== $block['blockName'] ) {
		return $block_content;
	}

	$replacements = array(
		'href="{{home_url}}"' => 'href="' . esc_url( home_url( '/' ) ) . '"',
	);

	return str_replace(
		array_keys( $replacements ),
		array_values( $replacements ),
		$block_content
	);
}
add_filter( 'render_block', 'amalfitana_render_template_placeholders', 10, 2 );

/**
 * Inject the dynamic Tours page URL into the header template part.
 *
 * Block theme template parts are HTML and cannot run inline PHP.
 */
function amalfitana_render_header_tours_link( $block_content, $block ) {
	if (
		empty( $block['blockName'] ) ||
		'core/template-part' !== $block['blockName'] ||
		empty( $block['attrs']['slug'] ) ||
		'header' !== $block['attrs']['slug']
	) {
		return $block_content;
	}

	return str_replace(
		'href="{{tours_url}}"',
		'href="' . amalfitana_get_tours_page_url() . '"',
		$block_content
	);
}
add_filter( 'render_block', 'amalfitana_render_header_tours_link', 10, 2 );

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
		'amalfitana-tour-card',
		get_template_directory_uri() . '/assets/css/tour-card.css',
		array( 'amalfitana-google-fonts', 'amalfitana-buttons' ),
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

	wp_enqueue_style(
		'amalfitana-tours-hero',
		get_template_directory_uri() . '/assets/css/tours-hero.css',
		array( 'amalfitana-google-fonts' ),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'amalfitana-tours-grid',
		get_template_directory_uri() . '/assets/css/tours-grid.css',
		array( 'amalfitana-google-fonts', 'amalfitana-tour-card' ),
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
