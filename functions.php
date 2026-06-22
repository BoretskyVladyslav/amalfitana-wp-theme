<?php
/**
 * Amalfitana Theme functions and definitions.
 *
 * @package Amalfitana_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$amalfitana_acf_fields_file = get_template_directory() . '/inc/acf-fields.php';
if ( file_exists( $amalfitana_acf_fields_file ) ) {
	require_once $amalfitana_acf_fields_file;
}

$amalfitana_front_page_acf_file = get_template_directory() . '/inc/front-page-acf.php';
if ( file_exists( $amalfitana_front_page_acf_file ) ) {
	require_once $amalfitana_front_page_acf_file;
}

$amalfitana_cpt_registration_file = get_template_directory() . '/inc/cpt-registration.php';
if ( file_exists( $amalfitana_cpt_registration_file ) ) {
	require_once $amalfitana_cpt_registration_file;
}

$amalfitana_experience_templates_file = get_template_directory() . '/inc/experience-templates.php';
if ( file_exists( $amalfitana_experience_templates_file ) ) {
	require_once $amalfitana_experience_templates_file;
}

$amalfitana_experience_editor_template_file = get_template_directory() . '/inc/experience-editor-template.php';
if ( file_exists( $amalfitana_experience_editor_template_file ) ) {
	require_once $amalfitana_experience_editor_template_file;
}

$amalfitana_experience_seed_file = get_template_directory() . '/inc/experience-seed.php';
if ( file_exists( $amalfitana_experience_seed_file ) ) {
	require_once $amalfitana_experience_seed_file;
}

/**
 * Register theme supports.
 */
function amalfitana_theme_setup() {
	add_theme_support( 'post-thumbnails', array( 'post', 'page', 'experience', 'testimonial' ) );
}
add_action( 'after_setup_theme', 'amalfitana_theme_setup' );

/**
 * Move testimonial Featured Image into the main column with a clear Ukrainian label.
 */
function amalfitana_reposition_testimonial_thumbnail_meta_box() {
	remove_meta_box( 'postimagediv', 'testimonial', 'side' );

	add_meta_box(
		'postimagediv',
		'Аватарка клієнта (Фото)',
		'post_thumbnail_meta_box',
		'testimonial',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'amalfitana_reposition_testimonial_thumbnail_meta_box', 20 );

/**
 * Custom title placeholders for FAQ and testimonial post types.
 *
 * @param string  $title Default placeholder text.
 * @param WP_Post $post  Current post object.
 * @return string
 */
function amalfitana_custom_enter_title_here( $title, $post ) {
	if ( ! $post instanceof WP_Post ) {
		return $title;
	}

	if ( 'faq' === $post->post_type ) {
		return 'Введіть питання...';
	}

	if ( 'testimonial' === $post->post_type ) {
		return "Ім'я автора відгуку...";
	}

	return $title;
}
add_filter( 'enter_title_here', 'amalfitana_custom_enter_title_here', 10, 2 );

/**
 * Remove cluttering native meta boxes from the Experience edit screen.
 */
function amalfitana_cleanup_experience_admin_meta_boxes() {
	remove_meta_box( 'slugdiv', 'experience', 'normal' );
}
add_action( 'add_meta_boxes', 'amalfitana_cleanup_experience_admin_meta_boxes', 99 );

/**
 * Register the Experience custom post type.
 */
function amalfitana_register_experience_post_type() {
	$labels = array(
		'name'               => 'Досвіди',
		'singular_name'      => 'Досвід',
		'menu_name'          => 'Досвіди',
		'name_admin_bar'     => 'Досвід',
		'add_new'            => 'Додати досвід',
		'add_new_item'       => 'Додати досвід',
		'edit_item'          => 'Редагувати досвід',
		'new_item'           => 'Новий досвід',
		'view_item'          => 'Переглянути досвід',
		'view_items'         => 'Переглянути досвіди',
		'search_items'       => 'Шукати досвіди',
		'not_found'          => 'Досвідів не знайдено',
		'not_found_in_trash' => 'У кошику досвідів не знайдено',
		'all_items'          => 'Всі досвіди',
	);

	register_post_type(
		'experience',
		array(
			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'has_archive'         => false,
			'rewrite'             => array(
				'slug'       => 'experiences',
				'with_front' => false,
			),
			'supports'            => array( 'title', 'thumbnail' ),
			'menu_icon'           => 'dashicons-location',
			'menu_position'       => 26,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'show_in_rest'        => false,
		)
	);
}
// Priority 20 ensures registration runs after ACF (init:5) and cannot be overwritten.
add_action( 'init', 'amalfitana_register_experience_post_type', 20 );

/**
 * Use the classic PHP single template for experiences (not the block HTML template).
 *
 * @param string $template Current template path.
 * @return string
 */
function amalfitana_force_experience_php_template( $template ) {
	if ( is_singular( 'experience' ) ) {
		$php_template = get_template_directory() . '/single-experience.php';

		if ( file_exists( $php_template ) ) {
			return $php_template;
		}
	}

	return $template;
}
add_filter( 'template_include', 'amalfitana_force_experience_php_template', 99 );

/**
 * Rebuild permalinks once when this theme is activated.
 */
function amalfitana_flush_rewrite_rules_on_theme_switch() {
	amalfitana_register_experience_post_type();

	if ( function_exists( 'amalfitana_register_content_post_types' ) ) {
		amalfitana_register_content_post_types();
	}

	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'amalfitana_flush_rewrite_rules_on_theme_switch' );

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
 * Allow SVG uploads for users who can access the media library.
 *
 * @param array<string, string> $mimes Allowed upload mime types.
 * @return array<string, string>
 */
function amalfitana_allow_svg_upload_mimes( $mimes ) {
	if ( current_user_can( 'upload_files' ) ) {
		$mimes['svg']  = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';
	}

	return $mimes;
}
add_filter( 'upload_mimes', 'amalfitana_allow_svg_upload_mimes' );

/**
 * Fix SVG mime detection for WordPress filetype checks.
 *
 * @param array<string, mixed> $data     Filetype data.
 * @param string               $file     Full path to the file.
 * @param string               $filename File name.
 * @param array<string, mixed> $mimes    Allowed mime types.
 * @return array<string, mixed>
 */
function amalfitana_fix_svg_filetype( $data, $file, $filename, $mimes ) {
	unset( $file, $mimes );

	$extension = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );

	if ( in_array( $extension, array( 'svg', 'svgz' ), true ) ) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}

	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'amalfitana_fix_svg_filetype', 10, 4 );

/**
 * Strip dangerous SVG markup before the file is saved.
 *
 * @param string $svg Raw SVG content.
 * @return string Sanitized SVG markup, or empty string when unsafe.
 */
function amalfitana_sanitize_svg_content( $svg ) {
	$svg = trim( (string) $svg );

	if ( '' === $svg || ! preg_match( '/<svg[\s>]/i', $svg ) ) {
		return '';
	}

	// Block XML entities, external DTDs, and embedded PHP.
	if ( preg_match( '/<!ENTITY|<\?(php|=)|<\?xml-stylesheet/i', $svg ) ) {
		return '';
	}

	if ( ! class_exists( 'DOMDocument' ) ) {
		return '';
	}

	$previous_setting = libxml_use_internal_errors( true );

	$dom = new DOMDocument();
	$dom->formatOutput     = false;
	$dom->preserveWhiteSpace = true;

	$loaded = $dom->loadXML( $svg, LIBXML_NONET | LIBXML_COMPACT );

	libxml_clear_errors();
	libxml_use_internal_errors( $previous_setting );

	if ( ! $loaded ) {
		return '';
	}

	$disallowed_tags = array(
		'script',
		'iframe',
		'object',
		'embed',
		'foreignObject',
		'audio',
		'video',
		'canvas',
		'form',
		'input',
		'button',
		'textarea',
		'select',
		'link',
		'meta',
		'base',
	);

	$xpath = new DOMXPath( $dom );

	foreach ( $disallowed_tags as $tag_name ) {
		$nodes = $dom->getElementsByTagName( $tag_name );

		for ( $index = $nodes->length - 1; $index >= 0; $index-- ) {
			$node = $nodes->item( $index );
			if ( $node && $node->parentNode ) {
				$node->parentNode->removeChild( $node );
			}
		}
	}

	$all_nodes = $xpath->query( '//*' );

	if ( $all_nodes instanceof DOMNodeList ) {
		foreach ( $all_nodes as $node ) {
			if ( ! $node instanceof DOMElement ) {
				continue;
			}

			if ( $node->hasAttributes() ) {
				$attributes_to_remove = array();

				foreach ( $node->attributes as $attribute ) {
					if ( ! $attribute instanceof DOMAttr ) {
						continue;
					}

					$name  = strtolower( $attribute->name );
					$value = trim( $attribute->value );

					if ( 0 === strpos( $name, 'on' ) ) {
						$attributes_to_remove[] = $attribute->name;
						continue;
					}

					if ( in_array( $name, array( 'href', 'xlink:href', 'src', 'data', 'poster', 'formaction' ), true ) ) {
						if ( preg_match( '/^\s*(javascript:|data:text\/html|vbscript:)/i', $value ) ) {
							$attributes_to_remove[] = $attribute->name;
						}
					}
				}

				foreach ( $attributes_to_remove as $attribute_name ) {
					$node->removeAttribute( $attribute_name );
				}
			}
		}
	}

	$root = $dom->documentElement;

	if ( ! $root instanceof DOMElement || 'svg' !== strtolower( $root->tagName ) ) {
		return '';
	}

	return $dom->saveXML( $root );
}

/**
 * Sanitize SVG uploads before WordPress moves them into uploads/.
 *
 * @param array<string, mixed> $file Uploaded file data.
 * @return array<string, mixed>
 */
function amalfitana_sanitize_svg_upload( $file ) {
	if ( empty( $file['name'] ) || empty( $file['tmp_name'] ) ) {
		return $file;
	}

	$extension = strtolower( pathinfo( (string) $file['name'], PATHINFO_EXTENSION ) );

	if ( ! in_array( $extension, array( 'svg', 'svgz' ), true ) ) {
		return $file;
	}

	if ( ! current_user_can( 'upload_files' ) ) {
		$file['error'] = __( 'You are not allowed to upload SVG files.', 'amalfitana-theme' );
		return $file;
	}

	$svg = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

	if ( false === $svg ) {
		$file['error'] = __( 'Unable to read the SVG file.', 'amalfitana-theme' );
		return $file;
	}

	$sanitized = amalfitana_sanitize_svg_content( $svg );

	if ( '' === $sanitized ) {
		$file['error'] = __( 'This SVG file contains unsupported or unsafe content.', 'amalfitana-theme' );
		return $file;
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	if ( false === file_put_contents( $file['tmp_name'], $sanitized ) ) {
		$file['error'] = __( 'Unable to save the sanitized SVG file.', 'amalfitana-theme' );
	}

	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'amalfitana_sanitize_svg_upload' );

/**
 * Ensure SVG attachments preview correctly in the media modal.
 *
 * @param array<string, mixed> $response   Attachment JS data.
 * @param WP_Post              $attachment Attachment post object.
 * @param array<string, mixed> $meta       Attachment meta.
 * @return array<string, mixed>
 */
function amalfitana_prepare_svg_attachment_for_js( $response, $attachment, $meta ) {
	unset( $meta );

	if ( empty( $response['mime'] ) || 'image/svg+xml' !== $response['mime'] ) {
		return $response;
	}

	if ( empty( $response['sizes'] ) ) {
		$metadata = isset( $attachment->ID ) ? wp_get_attachment_metadata( $attachment->ID ) : array();

		$response['sizes'] = array(
			'full' => array(
				'url'         => $response['url'],
				'width'       => isset( $metadata['width'] ) ? (int) $metadata['width'] : 0,
				'height'      => isset( $metadata['height'] ) ? (int) $metadata['height'] : 0,
				'orientation' => 'portrait',
			),
		);
	}

	$response['icon'] = $response['url'];

	return $response;
}
add_filter( 'wp_prepare_attachment_for_js', 'amalfitana_prepare_svg_attachment_for_js', 10, 3 );

/**
 * Return the forced site favicon URL.
 *
 * Overrides the Customizer site icon on the front-end and in wp-admin.
 *
 * @return string
 */
function amalfitana_get_forced_favicon_url() {
	return 'https://www.yulianaamalfitana.com/wp-content/uploads/2026/06/favcion.svg';
}

/**
 * Force the site icon URL, bypassing the WordPress site icon attachment.
 *
 * @param string $url     Site icon URL.
 * @param int    $size    Requested size.
 * @param int    $blog_id Site ID.
 * @return string
 */
function amalfitana_force_site_favicon_url( $url, $size, $blog_id ) {
	unset( $url, $size, $blog_id );

	return amalfitana_get_forced_favicon_url();
}
add_filter( 'get_site_icon_url', 'amalfitana_force_site_favicon_url', 99, 3 );

/**
 * Replace default site icon meta tags with the forced favicon.
 *
 * @param string[] $meta_tags Default site icon link tags.
 * @return string[]
 */
function amalfitana_force_site_icon_meta_tags( $meta_tags ) {
	unset( $meta_tags );

	$url = esc_url( amalfitana_get_forced_favicon_url() );

	return array(
		sprintf( '<link rel="icon" href="%s" sizes="any" />', $url ),
		sprintf( '<link rel="shortcut icon" href="%s" />', $url ),
	);
}
add_filter( 'site_icon_meta_tags', 'amalfitana_force_site_icon_meta_tags', 99 );

/**
 * Ensure favicon tags are printed even when no Customizer site icon is saved.
 *
 * @param bool $has_site_icon Whether a site icon is configured.
 * @return bool
 */
function amalfitana_force_has_site_icon( $has_site_icon ) {
	unset( $has_site_icon );

	return true;
}
add_filter( 'has_site_icon', 'amalfitana_force_has_site_icon', 99 );

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
		'href="{{about_url}}"' => 'href="' . amalfitana_get_about_page_url() . '"',
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

	if ( is_page_template( 'page-tour-detail' ) || is_singular( 'experience' ) ) {
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

	if ( ! is_admin() && function_exists( 'is_checkout' ) && is_checkout() && ! is_wc_endpoint_url( 'order-pay' ) ) {
		wp_enqueue_style(
			'amalfitana-checkout',
			get_template_directory_uri() . '/assets/css/checkout.css',
			array( 'amalfitana-google-fonts' ),
			wp_get_theme()->get( 'Version' )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'amalfitana_enqueue_theme_styles' );
add_action( 'enqueue_block_editor_assets', 'amalfitana_enqueue_theme_styles' );

/**
 * Load tour detail styles in the block editor for Experience posts.
 */
function amalfitana_enqueue_experience_editor_styles() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if ( ! $screen || 'experience' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_style(
		'amalfitana-tour-detail-content-editor',
		get_template_directory_uri() . '/assets/css/tour-detail-content.css',
		array( 'amalfitana-google-fonts' ),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'enqueue_block_editor_assets', 'amalfitana_enqueue_experience_editor_styles', 20 );

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

	wp_enqueue_script(
		'amalfitana-booking-popup',
		get_template_directory_uri() . '/assets/js/booking-popup.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);

	if ( is_page_template( 'page-tour-detail' ) || is_singular( 'experience' ) ) {
		wp_enqueue_script(
			'amalfitana-tour-checkout',
			get_template_directory_uri() . '/assets/js/tour-checkout.js',
			array(),
			wp_get_theme()->get( 'Version' ),
			true
		);
	}

	if ( is_singular( 'experience' ) ) {
		wp_enqueue_style(
			'flatpickr',
			'https://cdn.jsdelivr.net/npm/flatpickr@4/dist/flatpickr.min.css',
			array(),
			'4.6.13'
		);

		wp_enqueue_script(
			'flatpickr',
			'https://cdn.jsdelivr.net/npm/flatpickr@4/dist/flatpickr.min.js',
			array(),
			'4.6.13',
			true
		);

		wp_enqueue_script(
			'amalfitana-tour-booking',
			get_template_directory_uri() . '/assets/js/tour-booking.js',
			array( 'jquery', 'flatpickr' ),
			wp_get_theme()->get( 'Version' ),
			true
		);

		wp_localize_script( 'amalfitana-tour-booking', 'tourBookingData', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'tour_booking_nonce' ),
			'tour_id' => get_the_ID(),
		) );
	}

	if ( is_front_page() ) {
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
			'amalfitana-front-page-sliders',
			get_template_directory_uri() . '/assets/js/front-page-sliders.js',
			array( 'swiper' ),
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

/**
 * Booking logic — WooCommerce cart integration.
 */
require_once get_theme_file_path( '/inc/booking-logic.php' );
