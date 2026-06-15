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
 * Create theme pages once if they do not exist yet.
 */
function amalfitana_maybe_create_theme_pages() {
	$pages = array(
		array(
			'title' => 'Tours',
			'slug'  => 'tours',
		),
		array(
			'title' => 'Про мене',
			'slug'  => 'about',
		),
		array(
			'title' => 'Відгуки',
			'slug'  => 'reviews',
		),
		array(
			'title'    => 'FAQ',
			'slug'     => 'faq',
			'template' => 'page-faq',
		),
		array(
			'title'    => 'Контакти',
			'slug'     => 'contacts',
			'template' => 'page-contacts',
		),
		array(
			'title'    => 'Dolce Vita Maiori – Castello San Nicola de Thoro-Plano',
			'slug'     => 'dolce-vita-maiori',
			'template' => 'page-tour-detail',
		),
	);

	foreach ( $pages as $page ) {
		$existing_page = get_page_by_path( $page['slug'] );

		if ( null !== $existing_page ) {
			if ( ! empty( $page['template'] ) ) {
				update_post_meta( $existing_page->ID, '_wp_page_template', $page['template'] );
			}
			continue;
		}

		$page_id = wp_insert_post(
			array(
				'post_title'  => $page['title'],
				'post_name'   => $page['slug'],
				'post_status' => 'publish',
				'post_type'   => 'page',
			)
		);

		if ( $page_id && ! is_wp_error( $page_id ) && ! empty( $page['template'] ) ) {
			update_post_meta( $page_id, '_wp_page_template', $page['template'] );
		}
	}
}
add_action( 'init', 'amalfitana_maybe_create_theme_pages' );

/**
 * Return the canonical URL for the Tours page.
 */
function amalfitana_get_tours_page_url() {
	return esc_url( home_url( '/tours/' ) );
}

/**
 * Return the canonical URL for the About page.
 */
function amalfitana_get_about_page_url() {
	return esc_url( home_url( '/about/' ) );
}

/**
 * Return the canonical URL for the Reviews page.
 */
function amalfitana_get_reviews_page_url() {
	return esc_url( home_url( '/reviews/' ) );
}

/**
 * Return the canonical URL for the FAQ page.
 */
function amalfitana_get_faq_page_url() {
	return esc_url( home_url( '/faq/' ) );
}

/**
 * Return the canonical URL for the Contacts page.
 */
function amalfitana_get_contacts_page_url() {
	return esc_url( home_url( '/contacts/' ) );
}

/**
 * Resolve a media library attachment URL by filename at the requested size.
 *
 * Falls back to the uploads folder when no attachment is found.
 *
 * @param string $filename Basename of the uploaded file (e.g. hero-bg.png).
 * @param string $size     WordPress image size; defaults to full.
 * @return string Escaped URL.
 */
function amalfitana_get_media_url_by_filename( $filename, $size = 'full' ) {
	global $wpdb;

	$attachment_id = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' AND guid LIKE %s ORDER BY ID DESC LIMIT 1",
			'%' . $wpdb->esc_like( $filename )
		)
	);

	if ( $attachment_id ) {
		$attachment_id = (int) $attachment_id;
		$src           = wp_get_attachment_image_src( $attachment_id, 'full' );

		if ( ! empty( $src[0] ) ) {
			$url = $src[0];

			if ( function_exists( 'wp_get_original_image_url' ) ) {
				$original_url = wp_get_original_image_url( $attachment_id );
				if ( $original_url ) {
					$url = $original_url;
				}
			}

			return esc_url( $url );
		}
	}

	return esc_url( content_url( 'uploads/2026/06/' . $filename ) );
}

/**
 * Replace template placeholders in HTML blocks.
 */
function amalfitana_render_template_placeholders( $block_content, $block ) {
	if ( empty( $block['blockName'] ) || 'core/html' !== $block['blockName'] ) {
		return $block_content;
	}

	$hero_media = array(
		'hero-bg.png'    => '{{hero_bg_url}}',
		'round-img1.png' => '{{round_img1_url}}',
		'round-img2.png' => '{{round_img2_url}}',
		'round-img3.png' => '{{round_img3_url}}',
		'round-img4.png' => '{{round_img4_url}}',
		'round-img5.png' => '{{round_img5_url}}',
		'round-img6.png' => '{{round_img6_url}}',
		'round-img7.png' => '{{round_img7_url}}',
	);

	$replacements = array(
		'href="{{home_url}}"'  => 'href="' . esc_url( home_url( '/' ) ) . '"',
		'href="{{tours_url}}"' => 'href="' . amalfitana_get_tours_page_url() . '"',
	);

	foreach ( $hero_media as $filename => $placeholder ) {
		$replacements[ $placeholder ] = amalfitana_get_media_url_by_filename( $filename, 'full' );
	}

	return str_replace(
		array_keys( $replacements ),
		array_values( $replacements ),
		$block_content
	);
}
add_filter( 'render_block', 'amalfitana_render_template_placeholders', 10, 2 );

/**
 * Inject dynamic page URLs into the header template part.
 *
 * Block theme template parts are HTML and cannot run inline PHP.
 */
function amalfitana_render_header_nav_links( $block_content, $block ) {
	if (
		empty( $block['blockName'] ) ||
		'core/template-part' !== $block['blockName'] ||
		empty( $block['attrs']['slug'] ) ||
		'header' !== $block['attrs']['slug']
	) {
		return $block_content;
	}

	$replacements = array(
		'href="{{home_url}}"'    => 'href="' . esc_url( home_url( '/' ) ) . '"',
		'href="{{tours_url}}"'   => 'href="' . amalfitana_get_tours_page_url() . '"',
		'href="{{about_url}}"'   => 'href="' . amalfitana_get_about_page_url() . '"',
		'href="{{reviews_url}}"' => 'href="' . amalfitana_get_reviews_page_url() . '"',
		'href="{{faq_url}}"'     => 'href="' . amalfitana_get_faq_page_url() . '"',
		'href="{{contacts_url}}"' => 'href="' . amalfitana_get_contacts_page_url() . '"',
	);

	return str_replace(
		array_keys( $replacements ),
		array_values( $replacements ),
		$block_content
	);
}
add_filter( 'render_block', 'amalfitana_render_header_nav_links', 10, 2 );

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
		'amalfitana-forms',
		get_template_directory_uri() . '/assets/css/forms.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'amalfitana-footer',
		get_template_directory_uri() . '/assets/css/footer.css',
		array( 'amalfitana-google-fonts' ),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'amalfitana-animations',
		get_template_directory_uri() . '/assets/css/animations.css',
		array(),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'amalfitana-testimonial-card',
		get_template_directory_uri() . '/assets/css/testimonial-card.css',
		array( 'amalfitana-google-fonts' ),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'amalfitana-reviews-grid',
		get_template_directory_uri() . '/assets/css/reviews-grid.css',
		array( 'amalfitana-google-fonts', 'amalfitana-testimonial-card', 'amalfitana-animations' ),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'amalfitana-tours-hero',
		get_template_directory_uri() . '/assets/css/tours-hero.css',
		array( 'amalfitana-google-fonts' ),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'amalfitana-about-hero',
		get_template_directory_uri() . '/assets/css/about-hero.css',
		array( 'amalfitana-google-fonts' ),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'amalfitana-reviews-hero',
		get_template_directory_uri() . '/assets/css/reviews-hero.css',
		array( 'amalfitana-google-fonts' ),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'amalfitana-tours-grid',
		get_template_directory_uri() . '/assets/css/tours-grid.css',
		array( 'amalfitana-google-fonts', 'amalfitana-tour-card' ),
		wp_get_theme()->get( 'Version' )
	);

	if ( is_page_template( 'page-tour-detail' ) ) {
		wp_enqueue_style(
			'amalfitana-tour-detail-hero',
			get_template_directory_uri() . '/assets/css/tour-detail-hero.css',
			array( 'amalfitana-google-fonts', 'amalfitana-animations', 'amalfitana-tours-grid' ),
			wp_get_theme()->get( 'Version' )
		);

		wp_enqueue_style(
			'amalfitana-tour-detail-content',
			get_template_directory_uri() . '/assets/css/tour-detail-content.css',
			array( 'amalfitana-google-fonts', 'amalfitana-animations', 'amalfitana-buttons' ),
			wp_get_theme()->get( 'Version' )
		);
	}

	if ( is_page_template( 'page-faq' ) ) {
		wp_enqueue_style(
			'amalfitana-faq-page',
			get_template_directory_uri() . '/assets/css/faq-page.css',
			array( 'amalfitana-google-fonts', 'amalfitana-faq', 'amalfitana-animations', 'amalfitana-tours-grid' ),
			wp_get_theme()->get( 'Version' )
		);
	}

	if ( is_page_template( 'page-contacts' ) ) {
		wp_enqueue_style(
			'amalfitana-contacts-page',
			get_template_directory_uri() . '/assets/css/contacts-page.css',
			array( 'amalfitana-google-fonts', 'amalfitana-animations', 'amalfitana-tours-grid', 'amalfitana-buttons', 'amalfitana-footer' ),
			wp_get_theme()->get( 'Version' )
		);

		wp_enqueue_script(
			'amalfitana-contacts-form',
			get_template_directory_uri() . '/assets/js/contacts-form.js',
			array(),
			wp_get_theme()->get( 'Version' ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'amalfitana_enqueue_theme_styles' );
add_action( 'enqueue_block_editor_assets', 'amalfitana_enqueue_theme_styles' );

/**
 * Enqueue theme scripts.
 */
function amalfitana_enqueue_theme_scripts() {
	wp_enqueue_script(
		'amalfitana-animations',
		get_template_directory_uri() . '/assets/js/animations.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);

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

	wp_enqueue_script(
		'amalfitana-forms',
		get_template_directory_uri() . '/assets/js/forms.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);

	if ( is_page_template( 'page-tour-detail' ) ) {
		wp_enqueue_script(
			'amalfitana-tour-checkout',
			get_template_directory_uri() . '/assets/js/tour-checkout.js',
			array(),
			wp_get_theme()->get( 'Version' ),
			true
		);
	}

	if ( is_page( 'about' ) ) {
		wp_enqueue_style(
			'swiper',
			'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
			array(),
			'11.2.10'
		);

		wp_enqueue_script(
			'swiper',
			'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
			array(),
			'11.2.10',
			true
		);

		wp_enqueue_script(
			'amalfitana-about-testimonials',
			get_template_directory_uri() . '/assets/js/about-testimonials.js',
			array( 'swiper' ),
			wp_get_theme()->get( 'Version' ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'amalfitana_enqueue_theme_scripts' );
