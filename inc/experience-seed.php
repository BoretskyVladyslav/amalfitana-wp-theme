<?php
/**
 * One-time seed for the Dolce Vita Maiori gold-standard experience.
 *
 * @package Amalfitana_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create or update the Dolce Vita reference experience post.
 */
function amalfitana_seed_dolce_vita_experience() {
	if ( get_option( 'amalfitana_dolce_vita_seeded' ) ) {
		return;
	}

	$slug      = 'dolce-vita-maiori';
	$existing  = get_page_by_path( $slug, OBJECT, 'experience' );
	$title     = 'Dolce Vita';
	$long_name = 'Dolce Vita Maiori – Castello San Nicola de Thoro-Plano';
	$content   = function_exists( 'amalfitana_get_experience_editorial_block_markup' )
		? amalfitana_get_experience_editorial_block_markup()
		: '';
	$blocks    = parse_blocks( $content );
	$raw_post  = serialize_blocks( $blocks );

	$postarr = array(
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_type'    => 'experience',
		'post_status'  => 'publish',
		'post_content' => $raw_post,
	);

	if ( $existing instanceof WP_Post ) {
		$postarr['ID'] = $existing->ID;
		$post_id       = wp_update_post( $postarr, true );
	} else {
		$post_id = wp_insert_post( $postarr, true );
	}

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return;
	}

	if ( function_exists( 'update_field' ) ) {
		update_field( 'experience_hero_subtitle', $long_name, $post_id );
		update_field( 'experience_duration', 'до 8 годин', $post_id );
		update_field( 'experience_price', 'від 550 €', $post_id );
		update_field( 'experience_format_short', 'Піша екскурсія', $post_id );
		update_field(
			'experience_included',
			"супровід і організація\nекскурсія замком із власником\nдопомога з логістикою\nlocal experience у фортеці",
			$post_id
		);
		update_field( 'experience_not_included', 'обід і дегустації, особисті витрати', $post_id );
		update_field(
			'experience_what_to_take_tooltip',
			'зручне взуття без підборів, воду, капелюх і SPF, камеру або телефон',
			$post_id
		);
		update_field(
			'experience_highlights',
			"Майорі — автентичне містечко Amalfi Coast\nCollegiata di Santa Maria a Mare\nPalazzo Mezzacapo та сади у формі мальтійського хреста\nпанорамна піша стежка серед лимонних садів\nCastello San Nicola de Thoro-Plano\nісторії про сарацинів і старі оборонні вежі\nнеймовірні панорами узбережжя\nзустріч із власником замку\nстаровинні бастіони, музеї та середньовічна атмосфера\nдомашній local lunch у фортеці\nдомашнє вино, лімончелло та локальні продукти\nфотолокації протягом усього маршруту",
			$post_id
		);
		update_field(
			'experience_what_to_take_list',
			"зручне взуття без підборів\nводу\nкапелюх і SPF\nкамеру або телефон\nі готовність трохи закохатися в Amalfi Coast ще сильніше",
			$post_id
		);
		update_field(
			'experience_recommendation',
			'<p>Цю прогулянку особливо рекомендую тим, хто хоче побачити Amalfi Coast глибше: не лише красиві листівки, а місця з історією, характером і душею.</p>',
			$post_id
		);
	}

	$hero_url = 'https://www.yulianaamalfitana.com/wp-content/uploads/2026/06/Tours-hero__img.png';
	$hero_id  = attachment_url_to_postid( $hero_url );

	if ( ! $hero_id ) {
		$hero_id = media_sideload_image( $hero_url, $post_id, $title, 'id' );
		if ( is_wp_error( $hero_id ) ) {
			$hero_id = 0;
		}
	}

	if ( $hero_id ) {
		set_post_thumbnail( $post_id, $hero_id );
	}

	update_option( 'amalfitana_dolce_vita_seeded', 1, false );
}
add_action( 'admin_init', 'amalfitana_seed_dolce_vita_experience' );
