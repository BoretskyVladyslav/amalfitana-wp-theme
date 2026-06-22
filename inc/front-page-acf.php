<?php
/**
 * Front page ACF rendering helpers.
 *
 * @package Amalfitana_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve the front page post ID for ACF lookups.
 *
 * @return int
 */
function amalfitana_get_front_page_id() {
	static $front_page_id = null;

	if ( null !== $front_page_id && $front_page_id > 0 ) {
		return $front_page_id;
	}

	$front_page_id = (int) get_option( 'page_on_front' );

	if ( ! $front_page_id && is_front_page() ) {
		$front_page_id = (int) get_queried_object_id();
	}

	if ( ! $front_page_id && is_home() && ! is_paged() ) {
		$queried_id = (int) get_queried_object_id();
		if ( $queried_id > 0 ) {
			$front_page_id = $queried_id;
		}
	}

	return max( 0, (int) $front_page_id );
}

/**
 * Detect front-page ACF placeholders inside rendered HTML.
 *
 * @param string $html Rendered block HTML.
 * @return bool
 */
function amalfitana_block_has_front_page_acf_placeholders( $html ) {
	if ( ! is_string( $html ) || false === strpos( $html, '{{' ) ) {
		return false;
	}

	static $needles = null;

	if ( null === $needles ) {
		$needles = array(
			'{{hero_title}}',
			'{{hero_title_accent}}',
			'{{hero_feature_1}}',
			'{{hero_feature_2}}',
			'{{hero_feature_3}}',
			'{{hero_primary_cta_label}}',
			'{{hero_whatsapp_cta_label}}',
			'{{hero_background_video}}',
			'{{hero_video_class}}',
			'{{about_section_title}}',
			'{{about_bio_content}}',
			'{{about_card_1_title}}',
			'{{about_card_1_text}}',
			'{{about_card_2_title}}',
			'{{about_card_2_text}}',
			'{{about_button_label}}',
			'{{about_media}}',
			'{{home_tours_slider_cards}}',
			'{{home_tours_mobile_slides}}',
			'{{home_faq_list}}',
			'{{home_testimonials_list}}',
		);
	}

	foreach ( $needles as $needle ) {
		if ( false !== strpos( $html, $needle ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Decide whether front-page ACF replacements should run for this HTML pass.
 *
 * @param string     $html  Rendered block HTML.
 * @param array|null $block Optional block data from render_block.
 * @return bool
 */
function amalfitana_should_apply_front_page_acf_replacements( $html, $block = null ) {
	if ( ! amalfitana_block_has_front_page_acf_placeholders( $html ) ) {
		return false;
	}

	if ( amalfitana_is_front_page_render_context( $block ) ) {
		return true;
	}

	// Placeholders only exist in front-page templates; replace them whenever a
	// static front page is configured, even if is_front_page() is late on some hosts.
	if ( (int) get_option( 'page_on_front' ) > 0 ) {
		return true;
	}

	return is_home() && ! is_paged();
}

/**
 * Determine whether the current render pass is for the static front page.
 *
 * FSE block rendering can run before is_front_page() is reliable on some hosts,
 * so this helper combines query flags, post IDs, block context, template slug,
 * and the homepage request URI.
 *
 * @param array|null $block Optional block data from render_block.
 * @return bool
 */
function amalfitana_is_front_page_render_context( $block = null ) {
	$front_page_id = (int) get_option( 'page_on_front' );

	if ( is_front_page() ) {
		return true;
	}

	if ( $front_page_id > 0 ) {
		if ( is_page( $front_page_id ) ) {
			return true;
		}

		$queried_id = (int) get_queried_object_id();
		if ( $queried_id === $front_page_id ) {
			return true;
		}

		global $post;
		if ( $post instanceof WP_Post && (int) $post->ID === $front_page_id ) {
			return true;
		}

		if ( is_array( $block ) && ! empty( $block['context']['postId'] ) ) {
			if ( (int) $block['context']['postId'] === $front_page_id ) {
				return true;
			}
		}

		if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
			global $_wp_current_template_id;
			if ( ! empty( $_wp_current_template_id ) && false !== strpos( (string) $_wp_current_template_id, 'front-page' ) ) {
				return true;
			}
		}

		if ( ! is_admin() && isset( $_SERVER['REQUEST_URI'] ) ) {
			$home_path = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
			$req_path  = wp_parse_url( home_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH );

			if ( $home_path && $req_path && untrailingslashit( $req_path ) === untrailingslashit( $home_path ) ) {
				return true;
			}
		}

		return false;
	}

	return is_home() && ! is_paged();
}

/**
 * Normalize ACF image/file values into a URL string.
 *
 * @param mixed $value ACF field value.
 * @return string
 */
function amalfitana_resolve_acf_media_url( $value ) {
	if ( empty( $value ) ) {
		return '';
	}

	if ( is_string( $value ) ) {
		return trim( $value );
	}

	if ( is_numeric( $value ) ) {
		$url = wp_get_attachment_url( (int) $value );

		return $url ? $url : '';
	}

	if ( is_array( $value ) ) {
		if ( ! empty( $value['url'] ) ) {
			return trim( (string) $value['url'] );
		}

		$attachment_id = 0;

		if ( ! empty( $value['ID'] ) ) {
			$attachment_id = (int) $value['ID'];
		} elseif ( ! empty( $value['id'] ) ) {
			$attachment_id = (int) $value['id'];
		}

		if ( $attachment_id ) {
			$url = wp_get_attachment_url( $attachment_id );

			return $url ? $url : '';
		}
	}

	return '';
}

/**
 * Read an ACF field from the static front page.
 *
 * @param string $field_name ACF field name.
 * @param mixed  $default    Value when ACF is unavailable or empty.
 * @return mixed
 */
function amalfitana_get_home_field( $field_name, $default = '' ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$front_page_id = amalfitana_get_front_page_id();

	if ( ! $front_page_id ) {
		return $default;
	}

	$value = get_field( $field_name, $front_page_id );

	if ( null === $value || false === $value ) {
		return $default;
	}

	if ( is_string( $value ) && '' === trim( $value ) ) {
		return $default;
	}

	if ( is_array( $value ) && empty( $value ) ) {
		return $default;
	}

	return $value;
}

/**
 * Read a front page field with a post-meta fallback.
 *
 * @param string $field_name ACF/meta field name.
 * @return mixed
 */
function amalfitana_get_home_field_raw( $field_name ) {
	$front_page_id = amalfitana_get_front_page_id();

	if ( ! $front_page_id ) {
		return null;
	}

	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $field_name, $front_page_id );

		if ( null !== $value && false !== $value && ( ! is_string( $value ) || '' !== trim( $value ) ) && ( ! is_array( $value ) || ! empty( $value ) ) ) {
			return $value;
		}
	}

	$meta_value = get_post_meta( $front_page_id, $field_name, true );

	if ( null !== $meta_value && false !== $meta_value && ( ! is_string( $meta_value ) || '' !== trim( $meta_value ) ) && ( ! is_array( $meta_value ) || ! empty( $meta_value ) ) ) {
		return $meta_value;
	}

	return null;
}

/**
 * Normalize a WhatsApp number or link into a wa.me URL.
 *
 * @param string $value Phone number or WhatsApp URL.
 * @return string
 */
function amalfitana_normalize_whatsapp_url( $value ) {
	$value = trim( (string) $value );

	if ( '' === $value ) {
		return 'https://wa.me/393279140443';
	}

	if ( preg_match( '#^https?://#i', $value ) ) {
		return esc_url( $value );
	}

	$digits = preg_replace( '/[^\d+]/', '', $value );

	if ( '' === $digits ) {
		return '#';
	}

	return esc_url( 'https://wa.me/' . ltrim( $digits, '+' ) );
}

/**
 * Return a sanitized social/contact URL with fallback.
 *
 * @param string $field_name ACF field name.
 * @param string $fallback   Fallback URL.
 * @return string
 */
function amalfitana_get_contact_url( $field_name, $fallback = '#' ) {
	$url = trim( (string) amalfitana_get_home_field( $field_name, '' ) );

	return '' !== $url ? esc_url( $url ) : esc_url( $fallback );
}

/**
 * Return the site contact email with fallback.
 *
 * @param string $fallback Default email address.
 * @return string
 */
function amalfitana_get_contact_email( $fallback = 'amalfitana@gmail.com' ) {
	$email = trim( (string) amalfitana_get_home_field( 'contact_email', '' ) );

	return is_email( $email ) ? $email : $fallback;
}

/**
 * Return the site contact phone number with fallback.
 *
 * @param string $fallback Default phone number.
 * @return string
 */
function amalfitana_get_contact_phone( $fallback = '+393279140443' ) {
	if ( ! amalfitana_get_front_page_id() ) {
		return $fallback;
	}

	$phone_raw = amalfitana_get_home_field_raw( 'contact_phone' );
	$phone     = is_string( $phone_raw ) ? trim( $phone_raw ) : '';

	return '' !== $phone ? $phone : $fallback;
}

/**
 * Normalize a phone number for tel: links.
 *
 * @param string $phone Raw phone number.
 * @return string
 */
function amalfitana_clean_phone_for_tel( $phone ) {
	$phone = trim( (string) $phone );

	if ( '' === $phone ) {
		return '';
	}

	$has_plus   = 0 === strpos( $phone, '+' );
	$digits_only = preg_replace( '/\D+/', '', $phone );

	return $has_plus ? '+' . $digits_only : $digits_only;
}

/**
 * Return a tel: URL for the site contact phone.
 *
 * @return string
 */
function amalfitana_get_contact_phone_url() {
	$phone_clean = amalfitana_clean_phone_for_tel( amalfitana_get_contact_phone() );

	return '' !== $phone_clean ? esc_url( 'tel:' . $phone_clean ) : '#';
}

/**
 * Return a human-readable WhatsApp number for display.
 *
 * @return string
 */
function amalfitana_get_whatsapp_display() {
	$raw = trim( (string) amalfitana_get_home_field( 'contact_whatsapp', '' ) );

	if ( '' === $raw ) {
		return '+393279140443';
	}

	if ( preg_match( '#^https?://#i', $raw ) && preg_match( '#/(\d+)#', $raw, $matches ) ) {
		return '+' . $matches[1];
	}

	return $raw;
}

/**
 * Placeholder replacements for site-wide contact and social links.
 *
 * @return array<string, string>
 */
function amalfitana_get_site_contact_replacements() {
	if ( ! amalfitana_get_front_page_id() ) {
		return array();
	}

	$email       = amalfitana_get_contact_email();
	$phone_url   = amalfitana_get_contact_phone_url();
	$phone_clean = amalfitana_clean_phone_for_tel( amalfitana_get_contact_phone() );

	return array(
		'{{whatsapp_url}}'                   => amalfitana_normalize_whatsapp_url( amalfitana_get_home_field( 'contact_whatsapp', '' ) ),
		'{{contact_instagram_url}}'          => amalfitana_get_contact_url( 'contact_instagram', '#' ),
		'{{contact_telegram_url}}'           => amalfitana_get_contact_url( 'contact_telegram', '#' ),
		'{{contact_email_url}}'              => esc_url( 'mailto:' . $email ),
		'{{contact_phone_url}}'              => $phone_url,
		'href="{{contact_instagram_url}}"'   => 'href="' . amalfitana_get_contact_url( 'contact_instagram', '#' ) . '"',
		'href="{{contact_telegram_url}}"'    => 'href="' . amalfitana_get_contact_url( 'contact_telegram', '#' ) . '"',
		'href="{{contact_email_url}}"'       => 'href="' . esc_url( 'mailto:' . $email ) . '"',
		'href="{{contact_phone_url}}"'       => 'href="' . $phone_url . '"',
		'href="tel:{{contact_phone_clean}}"' => 'href="tel:' . esc_attr( $phone_clean ) . '"',
		'{{contact_email_display}}'          => esc_html( strtoupper( $email ) ),
		'{{contact_whatsapp_display}}'       => esc_html( amalfitana_get_whatsapp_display() ),
		'{{contact_phone_display}}'          => esc_html( amalfitana_get_contact_phone() ),
		'{{contact_phone_clean}}'            => esc_attr( $phone_clean ),
	);
}

/**
 * Replace contact placeholders inside rendered block HTML.
 *
 * @param string $block_content Rendered block HTML.
 * @return string
 */
function amalfitana_apply_site_contact_replacements( $block_content ) {
	$replacements = amalfitana_get_site_contact_replacements();

	if ( empty( $replacements ) ) {
		return $block_content;
	}

	return str_replace(
		array_keys( $replacements ),
		array_values( $replacements ),
		$block_content
	);
}

/**
 * Inject contact placeholders into HTML blocks and the footer template part.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Block data.
 * @return string
 */
function amalfitana_render_site_contact_links( $block_content, $block ) {
	if ( empty( $block['blockName'] ) || ! amalfitana_get_front_page_id() ) {
		return $block_content;
	}

	$should_replace = 'core/html' === $block['blockName'];

	if (
		! $should_replace &&
		'core/template-part' === $block['blockName'] &&
		! empty( $block['attrs']['slug'] )
	) {
		$should_replace = true;
	}

	if ( ! $should_replace ) {
		return $block_content;
	}

	return amalfitana_apply_site_contact_replacements( $block_content );
}
add_filter( 'render_block', 'amalfitana_render_site_contact_links', 15, 2 );

/**
 * Return the hero background video URL from ACF.
 *
 * @return string
 */
function amalfitana_get_hero_background_video_url() {
	$front_page_id = amalfitana_get_front_page_id();

	if ( ! $front_page_id ) {
		return '';
	}

	$url = amalfitana_resolve_acf_media_url( amalfitana_get_home_field_raw( 'hero_background_video_url' ) );

	if ( '' === $url ) {
		$url = amalfitana_resolve_acf_media_url( amalfitana_get_home_field_raw( 'hero_background_video' ) );
	}

	return $url;
}

/**
 * Return the hero poster/fallback image URL.
 *
 * @return string
 */
function amalfitana_get_hero_poster_url() {
	return amalfitana_get_media_url_by_filename( 'hero-bg.png', 'full' );
}

/**
 * Decide whether the hero background video should be rendered.
 *
 * Skips the video on mobile devices so the lighter static poster image is
 * served instead, protecting mobile performance and data usage.
 *
 * @return bool
 */
function amalfitana_should_render_hero_video() {
	if ( '' === amalfitana_get_hero_background_video_url() ) {
		return false;
	}

	if ( function_exists( 'wp_is_mobile' ) && wp_is_mobile() ) {
		return false;
	}

	return true;
}

/**
 * Return a modifier class when the hero uses a background video.
 *
 * @return string
 */
function amalfitana_get_hero_video_class() {
	return amalfitana_should_render_hero_video() ? ' hero--has-video' : '';
}

/**
 * Render the hero background video element.
 *
 * @return string
 */
function amalfitana_render_hero_background_video() {
	if ( ! amalfitana_should_render_hero_video() ) {
		return '';
	}

	$url = amalfitana_get_hero_background_video_url();

	return sprintf(
		'<video class="hero__video" src="%1$s" poster="%2$s" preload="metadata" autoplay muted loop playsinline aria-hidden="true"></video>',
		esc_url( $url ),
		esc_url( amalfitana_get_hero_poster_url() )
	);
}

/**
 * Return the hero main title with static fallback.
 *
 * @return string
 */
function amalfitana_get_hero_title() {
	return (string) amalfitana_get_home_field( 'hero_title', 'Amalfitana' );
}

/**
 * Return the hero accent word with static fallback.
 *
 * @return string
 */
function amalfitana_get_hero_title_accent() {
	return (string) amalfitana_get_home_field( 'hero_title_accent', 'experience' );
}

/**
 * Return the hero primary CTA label with static fallback.
 *
 * @return string
 */
function amalfitana_get_hero_primary_cta_label() {
	return (string) amalfitana_get_home_field( 'hero_primary_cta_label', 'Створити мій день на узбережжі' );
}

/**
 * Return the hero WhatsApp CTA label with static fallback.
 *
 * @return string
 */
function amalfitana_get_hero_whatsapp_cta_label() {
	return (string) amalfitana_get_home_field( 'hero_whatsapp_cta_label', 'Написати у WhatsApp' );
}

/**
 * Default copy for one hero feature bullet.
 *
 * @param int $number Feature index from 1 to 3.
 * @return string
 */
function amalfitana_get_hero_feature_default( $number ) {
	$defaults = array(
		1 => "Амальфітанські\nпригоди",
		2 => "Авторські подорожі\nКонсьерж",
		3 => "Пригоди\nдегустації",
	);

	$number = (int) $number;

	return isset( $defaults[ $number ] ) ? $defaults[ $number ] : '';
}

/**
 * Render one hero feature bullet with nl2br line breaks.
 *
 * @param int $number Feature index from 1 to 3.
 * @return string
 */
function amalfitana_render_hero_feature( $number ) {
	$number = (int) $number;

	if ( $number < 1 || $number > 3 ) {
		return '';
	}

	$field_name = 'hero_feature_' . $number;
	$default    = amalfitana_get_hero_feature_default( $number );
	$text       = trim( (string) amalfitana_get_home_field( $field_name, $default ) );

	if ( '' === $text ) {
		$text = $default;
	}

	return nl2br( esc_html( str_replace( array( "\r\n", "\r" ), "\n", $text ) ) );
}

/**
 * Return an about feature card title with static fallback.
 *
 * @param int    $card_number Card number (1 or 2).
 * @param string $fallback    Default title.
 * @return string
 */
function amalfitana_get_about_card_title( $card_number, $fallback ) {
	return (string) amalfitana_get_home_field( 'about_card_' . (int) $card_number . '_title', $fallback );
}

/**
 * Return an about feature card description with static fallback.
 *
 * @param int    $card_number Card number (1 or 2).
 * @param string $fallback    Default description.
 * @return string
 */
function amalfitana_get_about_card_text( $card_number, $fallback ) {
	return (string) amalfitana_get_home_field( 'about_card_' . (int) $card_number . '_text', $fallback );
}

/**
 * Return the about section button label with static fallback.
 *
 * @return string
 */
function amalfitana_get_about_button_label() {
	return (string) amalfitana_get_home_field( 'about_button_label', 'Більше про мене' );
}

/**
 * Return the about section title with static fallback.
 *
 * @return string
 */
function amalfitana_get_about_section_title() {
	$front_page_id = amalfitana_get_front_page_id();
	$title         = '';

	if ( $front_page_id && function_exists( 'get_field' ) ) {
		$title = trim( (string) get_field( 'about_section_title', $front_page_id ) );
	}

	return '' !== $title ? $title : 'Про мене';
}

/**
 * Default about bio paragraphs when ACF content is empty.
 *
 * @return string
 */
function amalfitana_get_about_bio_fallback_html() {
	return implode(
		'',
		array(
			'<p class="animate-on-scroll">Мені здається, що моє життя складається з кількох глав. І кожна з них навчила мене цінувати людей, моменти та шлях, яким веде доля.</p>',
			'<p class="animate-on-scroll">Я народилася в Сумах, навчалася психології та історії, писала дипломну роботу про відкриття другого фронту — і тоді навіть не здогадувалася, що назви Maiori та Valico Chiunzi колись стануть для мене не просто рядками на папері, а частиною мого життя.</p>',
			'<p class="animate-on-scroll">Я прожила 12 років в Одесі, стала мамою двох чудових дітей, спробувала багато професій і завжди шукала себе. А потім війна змінила все. Я опинилася в Італії з дітьми, з валізами тривог і без чітких планів на завтра.</p>',
		)
	);
}

/**
 * Render the about section bio content from ACF.
 *
 * @return string
 */
function amalfitana_render_about_bio_content() {
	$front_page_id = amalfitana_get_front_page_id();
	$content       = '';

	if ( $front_page_id && function_exists( 'get_field' ) ) {
		$content = (string) get_field( 'about_main_text', $front_page_id );
	}

	if ( '' === trim( wp_strip_all_tags( $content ) ) ) {
		$fallback_content = amalfitana_get_home_field_raw( 'about_main_text' );
		$content          = is_string( $fallback_content ) ? $fallback_content : '';
	}

	if ( '' === trim( wp_strip_all_tags( $content ) ) ) {
		return amalfitana_get_about_bio_fallback_html();
	}

	$content = apply_filters( 'the_content', $content );
	$content = preg_replace( '/<p(?![^>]*\bclass=)([^>]*)>/i', '<p class="animate-on-scroll"$1>', $content );

	return wp_kses_post( $content );
}

/**
 * Return the default about section image fallback URL.
 *
 * @return string
 */
function amalfitana_get_about_image_fallback_url() {
	return amalfitana_get_media_url_by_filename( 'about-img.png', 'full' );
}

/**
 * Return a neutral avatar placeholder for testimonials without a featured image.
 *
 * @return string
 */
function amalfitana_get_testimonial_avatar_placeholder_url() {
	$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 70 70" fill="none" role="img" aria-hidden="true"><circle cx="35" cy="35" r="35" fill="#f5f5f5"/><circle cx="35" cy="35" r="34" stroke="#FFFFFF" stroke-width="1.5"/><circle cx="35" cy="26" r="9.5" fill="#FFFFFF"/><path d="M16 54.5c0-9.5 8-15.5 19-15.5s19 6 19 15.5" fill="#FFFFFF"/></svg>';

	return 'data:image/svg+xml,' . rawurlencode( $svg );
}

/**
 * Return star markup used by review cards.
 *
 * @param int $rating Star count from 1 to 5.
 * @return string
 */
function amalfitana_get_review_card_stars_markup( $rating = 5 ) {
	$rating    = max( 1, min( 5, (int) $rating ) );
	$star_path = 'M5.08337 4.26671L6.95004 1.85004C7.08337 1.67226 7.24171 1.54171 7.42504 1.45837C7.60837 1.37504 7.80004 1.33337 8.00004 1.33337C8.20004 1.33337 8.39171 1.37504 8.57504 1.45837C8.75837 1.54171 8.91671 1.67226 9.05004 1.85004L10.9167 4.26671L13.75 5.21671C14.0389 5.3056 14.2667 5.46949 14.4334 5.70837C14.6 5.94726 14.6834 6.21115 14.6834 6.50004C14.6834 6.63337 14.6639 6.76671 14.625 6.90004C14.5862 7.03337 14.5223 7.16115 14.4334 7.28337L12.6 9.88337L12.6667 12.6167C12.6778 13.0056 12.55 13.3334 12.2834 13.6C12.0167 13.8667 11.7056 14 11.35 14C11.3278 14 11.2056 13.9834 10.9834 13.95L8.00004 13.1167L5.01671 13.95C4.96115 13.9723 4.90004 13.9862 4.83337 13.9917C4.76671 13.9973 4.7056 14 4.65004 14C4.29448 14 3.98337 13.8667 3.71671 13.6C3.45004 13.3334 3.32226 13.0056 3.33337 12.6167L3.40004 9.86671L1.58337 7.28337C1.49449 7.16115 1.4306 7.03337 1.39171 6.90004C1.35282 6.76671 1.33337 6.63337 1.33337 6.50004C1.33337 6.22226 1.41393 5.96393 1.57504 5.72504C1.73615 5.48615 1.96115 5.31671 2.25004 5.21671L5.08337 4.26671Z';
	$star_svg  = '<svg class="review-card__star" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="' . $star_path . '" /></svg>';

	return sprintf(
		'<div class="review-card__stars" aria-label="%1$s">%2$s</div>',
		esc_attr(
			sprintf(
				/* translators: %d: star rating out of 5 */
				__( '%d out of 5 stars', 'amalfitana-theme' ),
				$rating
			)
		),
		str_repeat( $star_svg, $rating )
	);
}

/**
 * Render the about section media from ACF with a static image fallback.
 *
 * @return string
 */
function amalfitana_render_about_video_media() {
	$front_page_id = amalfitana_get_front_page_id();

	if ( ! $front_page_id ) {
		return amalfitana_render_about_image_fallback_markup();
	}

	$about_video = amalfitana_get_home_field_raw( 'about_video' );
	$video_url   = amalfitana_resolve_acf_media_url( $about_video );

	if ( '' !== $video_url ) {
		return sprintf(
			'<video src="%1$s" autoplay loop muted playsinline class="about__media-video" style="width:100%%; height:100%%; max-height:600px; object-fit:cover; aspect-ratio:3/4; border-radius:inherit;"></video>',
			esc_url( $video_url )
		);
	}

	$about_image = amalfitana_get_home_field_raw( 'about_author_image' );
	$image_url   = amalfitana_resolve_acf_media_url( $about_image );

	if ( '' === $image_url ) {
		$image_url = amalfitana_get_about_image_fallback_url();
	}

	return sprintf(
		'<img src="%1$s" alt="%2$s" class="about__media-element">',
		esc_url( $image_url ),
		esc_attr__( 'Guide on the Amalfi coast', 'amalfitana-theme' )
	);
}

/**
 * Return fallback about image markup when ACF is unavailable.
 *
 * @return string
 */
function amalfitana_render_about_image_fallback_markup() {
	return sprintf(
		'<img src="%1$s" alt="%2$s" class="about__media-element">',
		esc_url( amalfitana_get_about_image_fallback_url() ),
		esc_attr__( 'Guide on the Amalfi coast', 'amalfitana-theme' )
	);
}

/**
 * Format FAQ answer markup to match the static front-page template.
 *
 * @param string $answer_html Answer HTML from the editor.
 * @return string
 */
function amalfitana_format_faq_answer_markup( $answer_html ) {
	$answer_html = trim( (string) $answer_html );

	if ( '' === $answer_html ) {
		return '<p class="faq-item__answer"></p>';
	}

	if ( preg_match( '/^<p\b/i', $answer_html ) ) {
		return preg_replace( '/^<p\b/i', '<p class="faq-item__answer"', $answer_html, 1 );
	}

	return '<p class="faq-item__answer">' . wp_kses_post( $answer_html ) . '</p>';
}

/**
 * Render one FAQ accordion item.
 *
 * @param string $question FAQ question text.
 * @param string $answer   FAQ answer HTML.
 * @param int    $index    Zero-based item index.
 * @return string
 */
function amalfitana_render_home_faq_item( $question, $answer, $index ) {
	$item_number   = $index + 1;
	$answer_markup = amalfitana_format_faq_answer_markup( $answer );

	ob_start();
	?>
		<div class="faq-item">
			<h3 class="faq-item__heading">
				<button type="button" class="faq-item__trigger" id="faq-question-<?php echo esc_attr( (string) $item_number ); ?>" aria-expanded="false" aria-controls="faq-answer-<?php echo esc_attr( (string) $item_number ); ?>">
					<span class="faq-item__question"><?php echo esc_html( $question ); ?></span>
					<span class="faq-item__icon-wrap" aria-hidden="true">
						<span class="faq-item__icon" aria-hidden="true">
									<span class="faq-item__icon-line faq-item__icon-line--horizontal"></span>
									<span class="faq-item__icon-line faq-item__icon-line--vertical"></span>
								</span>
					</span>
				</button>
			</h3>
			<div class="faq-item__panel" id="faq-answer-<?php echo esc_attr( (string) $item_number ); ?>" role="region" aria-labelledby="faq-question-<?php echo esc_attr( (string) $item_number ); ?>">
				<div class="faq-item__panel-inner">
					<?php echo $answer_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in formatter. ?>
				</div>
			</div>
		</div>
	<?php
	return ob_get_clean();
}

/**
 * Default FAQ items when ACF data is empty.
 *
 * @return array<int, array{question: string, answer: string}>
 */
function amalfitana_get_home_faq_fallback_items() {
	return array(
		array(
			'question' => 'Наскільки наперед краще бронювати авторські подорожі?',
			'answer'   => '<p>////////////////////</p>',
		),
		array(
			'question' => 'Коли краще поїхати на узбережжя Амальфі?',
			'answer'   => '<p>////////////////////</p>',
		),
		array(
			'question' => 'Чи можна з дітьми?',
			'answer'   => '<p>////////////////////</p>',
		),
		array(
			'question' => 'Чи є індивідуальний трансфер?',
			'answer'   => '<p>////////////////////</p>',
		),
		array(
			'question' => 'Наскільки наперед краще бронювати авторські подорожі?',
			'answer'   => '<p>////////////////////</p>',
		),
	);
}

/**
 * Render the front page FAQ list from the faq CPT.
 *
 * @return string
 */
function amalfitana_render_home_faq_list() {
	$faq_query = new WP_Query(
		array(
			'post_type'      => 'faq',
			'post_status'    => 'publish',
			'posts_per_page' => 5,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
		)
	);

	$output = '';

	if ( $faq_query->have_posts() ) {
		$index = 0;

		while ( $faq_query->have_posts() ) {
			$faq_query->the_post();

			$faq_answer = function_exists( 'get_field' ) ? get_field( 'faq_answer' ) : '';

			if ( ! empty( $faq_answer ) ) {
				$faq_answer = apply_filters( 'the_content', $faq_answer );
			}

			$output .= amalfitana_render_home_faq_item(
				get_the_title(),
				$faq_answer,
				$index
			);

			$index++;
		}

		wp_reset_postdata();
	} else {
		foreach ( amalfitana_get_home_faq_fallback_items() as $index => $item ) {
			$output .= amalfitana_render_home_faq_item(
				$item['question'],
				$item['answer'],
				$index
			);
		}
	}

	return $output;
}

/**
 * Render one testimonial review card.
 *
 * @param string $text       Testimonial body HTML.
 * @param string $author     Testimonial author.
 * @param string $avatar_url Avatar image URL.
 * @param int    $rating     Star rating from 1 to 5.
 * @return string
 */
function amalfitana_render_home_testimonial_card( $text, $author, $avatar_url = '', $rating = 5 ) {
	if ( empty( $avatar_url ) ) {
		$avatar_url = amalfitana_get_testimonial_avatar_placeholder_url();
	}

	ob_start();
	?>
	<article class="review-card">
		<div class="review-card__message-box">
			<?php echo amalfitana_get_review_card_stars_markup( $rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG markup. ?>
			<p class="review-card__text"><?php echo wp_kses_post( $text ); ?></p>
		</div>
		<div class="review-card__author">
			<img class="review-card__avatar" src="<?php echo esc_attr( $avatar_url ); ?>" alt="<?php echo esc_attr( $author ); ?>" width="70" height="70" loading="lazy">
			<div class="review-card__author-details">
				<p class="review-card__name"><?php echo esc_html( $author ); ?></p>
			</div>
		</div>
	</article>
	<?php
	return ob_get_clean();
}

/**
 * Default testimonials when ACF data is empty.
 *
 * @return array<int, array{text: string, author: string}>
 */
function amalfitana_get_home_testimonials_fallback_items() {
	return array(
		array(
			'text'   => 'Дякую тобі величезне за цю пригоду. Пережиті емоції ні з чим незрівнянні. Було супер! Видосики хочеться дивитися на повторі.',
			'author' => 'Инга Слободнюк',
		),
	);
}

/**
 * Render the front page testimonials slider track from the testimonial CPT.
 *
 * @return string
 */
function amalfitana_render_home_testimonials_list() {
	$testimonial_query = new WP_Query(
		array(
			'post_type'      => 'testimonial',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'ASC',
		)
	);

	$output = '';

	if ( $testimonial_query->have_posts() ) {
		while ( $testimonial_query->have_posts() ) {
			$testimonial_query->the_post();

			$avatar_url = '';

			if ( has_post_thumbnail() ) {
				$avatar_url = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
			}

			$review_text   = function_exists( 'get_field' ) ? (string) get_field( 'review_text' ) : '';
			$review_rating = function_exists( 'get_field' ) ? (int) get_field( 'review_rating' ) : 5;

			if ( $review_rating < 1 || $review_rating > 5 ) {
				$review_rating = 5;
			}

			if ( '' !== $review_text ) {
				$review_text = wp_kses_post( nl2br( $review_text ) );
			}

			$output .= amalfitana_render_home_testimonial_card(
				$review_text,
				get_the_title(),
				$avatar_url,
				$review_rating
			);
		}

		wp_reset_postdata();
	} else {
		foreach ( amalfitana_get_home_testimonials_fallback_items() as $item ) {
			$output .= amalfitana_render_home_testimonial_card( $item['text'], $item['author'] );
		}
	}

	return $output;
}

/**
 * Build all front page ACF placeholder replacements.
 *
 * @return array<string, string>
 */
function amalfitana_get_front_page_acf_replacements() {
	static $replacements = null;

	if ( null !== $replacements ) {
		return $replacements;
	}

	$about_media_markup = amalfitana_render_about_video_media();

	$replacements = array(
		'{{about_media}}'              => $about_media_markup,
		'{{about_video_media}}'        => $about_media_markup,
		'{{about_image}}'              => $about_media_markup,
		'{{hero_background_video}}'    => amalfitana_render_hero_background_video(),
		'{{hero_video_class}}'         => amalfitana_get_hero_video_class(),
		'{{hero_title}}'               => esc_html( amalfitana_get_hero_title() ),
		'{{hero_title_accent}}'        => esc_html( amalfitana_get_hero_title_accent() ),
		'{{hero_feature_1}}'           => amalfitana_render_hero_feature( 1 ),
		'{{hero_feature_2}}'           => amalfitana_render_hero_feature( 2 ),
		'{{hero_feature_3}}'           => amalfitana_render_hero_feature( 3 ),
		'{{hero_primary_cta_label}}'   => esc_html( amalfitana_get_hero_primary_cta_label() ),
		'{{hero_whatsapp_cta_label}}'  => esc_html( amalfitana_get_hero_whatsapp_cta_label() ),
		'{{about_section_title}}'      => esc_html( amalfitana_get_about_section_title() ),
		'{{about_bio_content}}'        => amalfitana_render_about_bio_content(),
		'{{about_card_1_title}}'       => esc_html( amalfitana_get_about_card_title( 1, 'В Італії 5+ років' ) ),
		'{{about_card_1_text}}'        => esc_html( amalfitana_get_about_card_text( 1, 'Острів який за легендою виник від неможливого кохання.' ) ),
		'{{about_card_2_title}}'       => esc_html( amalfitana_get_about_card_title( 2, 'Знаю скриті місця' ) ),
		'{{about_card_2_text}}'        => esc_html( amalfitana_get_about_card_text( 2, 'Острів який за легендою виник від неможливого кохання.' ) ),
		'{{about_button_label}}'       => esc_html( amalfitana_get_about_button_label() ),
		'{{home_faq_list}}'            => amalfitana_render_home_faq_list(),
		'{{home_testimonials_list}}'   => amalfitana_render_home_testimonials_list(),
		'{{home_tours_slider_cards}}'  => function_exists( 'amalfitana_render_home_tours_slider_cards' ) ? amalfitana_render_home_tours_slider_cards() : '',
		'{{home_tours_mobile_slides}}' => function_exists( 'amalfitana_render_home_tours_mobile_slides' ) ? amalfitana_render_home_tours_mobile_slides() : '',
	);

	return $replacements;
}

/**
 * Apply front page ACF placeholder replacements to rendered HTML.
 *
 * @param string     $html  Rendered block HTML.
 * @param array|null $block Optional block data from render_block.
 * @return string
 */
function amalfitana_apply_front_page_acf_replacements( $html, $block = null ) {
	if ( ! is_string( $html ) || '' === $html || false === strpos( $html, '{{' ) ) {
		return $html;
	}

	if ( ! amalfitana_should_apply_front_page_acf_replacements( $html, $block ) ) {
		return $html;
	}

	$replacements = amalfitana_get_front_page_acf_replacements();

	return str_replace(
		array_keys( $replacements ),
		array_values( $replacements ),
		$html
	);
}

/**
 * Replace front page ACF placeholders inside rendered blocks.
 *
 * Runs on core/html blocks and the about-me template part on the homepage.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Block data.
 * @return string
 */
function amalfitana_render_front_page_acf_content( $block_content, $block ) {
	if ( empty( $block['blockName'] ) || ! is_string( $block_content ) ) {
		return $block_content;
	}

	if ( false === strpos( $block_content, '{{' ) ) {
		return $block_content;
	}

	$is_html_block = 'core/html' === $block['blockName'];
	$is_about_part = 'core/template-part' === $block['blockName']
		&& ! empty( $block['attrs']['slug'] )
		&& 'about-me' === $block['attrs']['slug'];

	if ( ! $is_html_block && ! $is_about_part && ! amalfitana_block_has_front_page_acf_placeholders( $block_content ) ) {
		return $block_content;
	}

	return amalfitana_apply_front_page_acf_replacements( $block_content, $block );
}
add_filter( 'render_block', 'amalfitana_render_front_page_acf_content', 20, 2 );
