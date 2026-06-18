<?php
/**
 * Custom post type registrations.
 *
 * @package Amalfitana_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register FAQ and Testimonial custom post types.
 */
function amalfitana_register_content_post_types() {
	register_post_type(
		'faq',
		array(
			'labels'              => array(
				'name'               => 'FAQ',
				'singular_name'      => 'FAQ',
				'menu_name'          => 'FAQ',
				'add_new'            => 'Додати питання',
				'add_new_item'       => 'Додати питання',
				'edit_item'          => 'Редагувати питання',
				'new_item'           => 'Нове питання',
				'view_item'          => 'Переглянути питання',
				'view_items'         => 'Переглянути FAQ',
				'search_items'       => 'Шукати питання',
				'not_found'          => 'Питань не знайдено',
				'not_found_in_trash' => 'У кошику питань не знайдено',
				'all_items'          => 'Всі питання',
			),
			'public'              => true,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'has_archive'         => false,
			'rewrite'             => false,
			'supports'            => array( 'title', 'page-attributes' ),
			'menu_icon'           => 'dashicons-editor-help',
			'menu_position'       => 27,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'show_in_rest'        => false,
		)
	);

	register_post_type(
		'testimonial',
		array(
			'labels'              => array(
				'name'               => 'Відгуки',
				'singular_name'      => 'Відгук',
				'menu_name'          => 'Відгуки',
				'add_new'            => 'Додати відгук',
				'add_new_item'       => 'Додати відгук',
				'edit_item'          => 'Редагувати відгук',
				'new_item'           => 'Новий відгук',
				'view_item'          => 'Переглянути відгук',
				'view_items'         => 'Переглянути відгуки',
				'search_items'       => 'Шукати відгуки',
				'not_found'          => 'Відгуків не знайдено',
				'not_found_in_trash' => 'У кошику відгуків не знайдено',
				'all_items'          => 'Всі відгуки',
			),
			'public'              => true,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'has_archive'         => false,
			'rewrite'             => false,
			'supports'            => array( 'title', 'thumbnail' ),
			'menu_icon'           => 'dashicons-format-quote',
			'menu_position'       => 28,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'show_in_rest'        => false,
		)
	);
}
add_action( 'init', 'amalfitana_register_content_post_types', 20 );
