<?php
/**
 * ACF local field groups registered via PHP.
 *
 * @package Amalfitana_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme ACF field groups.
 */
function amalfitana_register_acf_field_groups() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_amalfitana_experience_settings',
			'title'                 => 'Налаштування Досвіду',
			'fields'                => array(
				array(
					'key'               => 'field_amalfitana_exp_hero_subtitle',
					'label'             => 'Підзаголовок (на банері)',
					'name'              => 'experience_hero_subtitle',
					'type'              => 'text',
					'instructions'      => 'Розширений опис під золотим заголовком (напр. «Dolce Vita Maiori – Castello San Nicola de Thoro-Plano»). Коротку назву вводьте у полі «Заголовок» вище.',
					'required'          => 0,
					'maxlength'         => 120,
					'wrapper'           => array(
						'width' => '100',
					),
				),
				array(
					'key'               => 'field_amalfitana_exp_price',
					'label'             => 'Вартість (напр. від 600€)',
					'name'              => 'experience_price',
					'type'              => 'text',
					'instructions'      => 'Коротко. Якщо текст задовгий, на картці він буде акуратно обрізаний (2 рядки), а повністю покажеться на сторінці туру.',
					'required'          => 0,
					'wrapper'           => array(
						'width' => '33',
					),
				),
				array(
					'key'               => 'field_amalfitana_exp_duration',
					'label'             => 'Тривалість (напр. 1 - 3 дні)',
					'name'              => 'experience_duration',
					'type'              => 'text',
					'instructions'      => 'Коротко. Якщо текст задовгий, на картці він буде акуратно обрізаний (2 рядки), а повністю покажеться на сторінці туру.',
					'required'          => 0,
					'wrapper'           => array(
						'width' => '33',
					),
				),
				array(
					'key'               => 'field_amalfitana_exp_format_short',
					'label'             => 'Формат (1-2 слова)',
					'name'              => 'experience_format_short',
					'type'              => 'text',
					'instructions'      => 'Короткий формат для блоку метрик (напр. «Піша екскурсія»).',
					'required'          => 0,
					'maxlength'         => 40,
					'wrapper'           => array(
						'width' => '33',
					),
				),
				array(
					'key'               => 'field_amalfitana_exp_what_to_take_tooltip',
					'label'             => 'Що взяти з собою (підказка формату)',
					'name'              => 'experience_what_to_take_tooltip',
					'type'              => 'text',
					'instructions'      => 'Короткий текст у спливаючій підказці біля метрики «Формат».',
					'required'          => 0,
					'wrapper'           => array(
						'width' => '100',
					),
				),
				array(
					'key'               => 'field_amalfitana_exp_main_text',
					'label'             => 'Головний текст (Про подорож)',
					'name'              => 'experience_main_text',
					'type'              => 'wysiwyg',
					'instructions'      => 'Основний текст блоку «Про подорож» на сторінці досвіду.',
					'required'          => 0,
					'tabs'              => 'all',
					'toolbar'           => 'basic',
					'media_upload'      => 0,
					'delay'             => 0,
					'wrapper'           => array(
						'width' => '100',
					),
				),
				array(
					'key'               => 'field_amalfitana_exp_highlights',
					'label'             => 'Що вас чекає',
					'name'              => 'experience_highlights',
					'type'              => 'textarea',
					'instructions'      => 'Що вас чекає (кожен пункт з нового рядка).',
					'required'          => 0,
					'rows'              => 8,
					'new_lines'         => '',
					'wrapper'           => array(
						'width' => '50',
					),
				),
				array(
					'key'               => 'field_amalfitana_exp_what_to_take_list',
					'label'             => 'Що взяти з собою',
					'name'              => 'experience_what_to_take_list',
					'type'              => 'textarea',
					'instructions'      => 'Що взяти з собою (кожен пункт з нового рядка).',
					'required'          => 0,
					'rows'              => 6,
					'new_lines'         => '',
					'wrapper'           => array(
						'width' => '50',
					),
				),
				array(
					'key'               => 'field_amalfitana_exp_included',
					'label'             => 'У вартість входить',
					'name'              => 'experience_included',
					'type'              => 'textarea',
					'instructions'      => 'У вартість входить (кожен пункт з нового рядка).',
					'required'          => 0,
					'rows'              => 6,
					'new_lines'         => '',
					'wrapper'           => array(
						'width' => '50',
					),
				),
				array(
					'key'               => 'field_amalfitana_exp_not_included',
					'label'             => 'Не включено',
					'name'              => 'experience_not_included',
					'type'              => 'textarea',
					'instructions'      => 'Не включено (текстом).',
					'required'          => 0,
					'rows'              => 3,
					'new_lines'         => 'br',
					'wrapper'           => array(
						'width' => '50',
					),
				),
				array(
					'key'               => 'field_amalfitana_exp_recommendation',
					'label'             => 'Рекомендація',
					'name'              => 'experience_recommendation',
					'type'              => 'wysiwyg',
					'instructions'      => 'Рекомендація',
					'required'          => 0,
					'tabs'              => 'all',
					'toolbar'           => 'basic',
					'media_upload'      => 0,
					'delay'             => 0,
					'wrapper'           => array(
						'width' => '100',
					),
				),
				array(
					'key'               => 'field_amalfitana_exp_callout',
					'label'             => 'Фінальна цитата / Callout (опціонально)',
					'name'              => 'experience_callout',
					'type'              => 'textarea',
					'instructions'      => 'Виділена цитата внизу сторінки досвіду (перед боковою панеллю).',
					'required'          => 0,
					'rows'              => 3,
					'new_lines'         => '',
					'wrapper'           => array(
						'width' => '100',
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'experience',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'acf_after_title',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_amalfitana_review_settings',
			'title'                 => 'Налаштування Відгуку',
			'fields'                => array(
				array(
					'key'           => 'field_amalfitana_review_rating',
					'label'         => 'Оцінка (1-5)',
					'name'          => 'review_rating',
					'type'          => 'number',
					'required'      => 0,
					'min'           => 1,
					'max'           => 5,
					'step'          => 1,
					'default_value' => 5,
					'wrapper'       => array(
						'width' => '100',
					),
				),
				array(
					'key'       => 'field_amalfitana_review_text',
					'label'     => 'Текст відгуку',
					'name'      => 'review_text',
					'type'      => 'textarea',
					'required'  => 0,
					'rows'      => 6,
					'new_lines' => 'br',
					'wrapper'   => array(
						'width' => '100',
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'testimonial',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'acf_after_title',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_amalfitana_faq_settings',
			'title'                 => 'Налаштування FAQ',
			'fields'                => array(
				array(
					'key'          => 'field_amalfitana_faq_answer',
					'label'        => 'Відповідь',
					'name'         => 'faq_answer',
					'type'         => 'wysiwyg',
					'instructions' => 'Питання вводиться у полі «Заголовок» вище.',
					'required'     => 0,
					'tabs'         => 'all',
					'toolbar'      => 'basic',
					'media_upload' => 0,
					'delay'        => 0,
					'wrapper'      => array(
						'width' => '100',
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'faq',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'acf_after_title',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_amalfitana_home_content',
			'title'                 => 'Головна сторінка — Контент',
			'fields'                => array(
				array(
					'key'          => 'field_amalfitana_home_hero_title',
					'label'        => 'Hero: Заголовок',
					'name'         => 'hero_title',
					'type'         => 'text',
					'instructions' => 'Великий заголовок на головному екрані (напр. «Amalfitana»).',
					'required'     => 0,
					'wrapper'      => array(
						'width' => '50',
					),
				),
				array(
					'key'          => 'field_amalfitana_home_hero_title_accent',
					'label'        => 'Hero: Акцент (рукописне слово)',
					'name'         => 'hero_title_accent',
					'type'         => 'text',
					'instructions' => 'Рукописне слово поверх заголовка (напр. «experience»).',
					'required'     => 0,
					'wrapper'      => array(
						'width' => '50',
					),
				),
				array(
					'key'          => 'field_amalfitana_home_hero_feature_1',
					'label'        => 'Hero: Пункт 1',
					'name'         => 'hero_feature_1',
					'type'         => 'textarea',
					'instructions' => 'Перший пункт переваг. Натисніть Enter для перенесення на новий рядок.',
					'required'     => 0,
					'rows'         => 2,
					'new_lines'    => '',
					'wrapper'      => array(
						'width' => '33',
					),
				),
				array(
					'key'          => 'field_amalfitana_home_hero_feature_2',
					'label'        => 'Hero: Пункт 2',
					'name'         => 'hero_feature_2',
					'type'         => 'textarea',
					'instructions' => 'Другий пункт переваг. Натисніть Enter для перенесення на новий рядок.',
					'required'     => 0,
					'rows'         => 2,
					'new_lines'    => '',
					'wrapper'      => array(
						'width' => '33',
					),
				),
				array(
					'key'          => 'field_amalfitana_home_hero_feature_3',
					'label'        => 'Hero: Пункт 3',
					'name'         => 'hero_feature_3',
					'type'         => 'textarea',
					'instructions' => 'Третій пункт переваг. Натисніть Enter для перенесення на новий рядок.',
					'required'     => 0,
					'rows'         => 2,
					'new_lines'    => '',
					'wrapper'      => array(
						'width' => '33',
					),
				),
				array(
					'key'          => 'field_amalfitana_home_hero_primary_cta',
					'label'        => 'Hero: Текст головної кнопки',
					'name'         => 'hero_primary_cta_label',
					'type'         => 'text',
					'instructions' => 'Напис на кнопці бронювання (напр. «Створити мій день на узбережжі»).',
					'required'     => 0,
					'wrapper'      => array(
						'width' => '50',
					),
				),
				array(
					'key'          => 'field_amalfitana_home_hero_whatsapp_cta',
					'label'        => 'Hero: Текст кнопки WhatsApp',
					'name'         => 'hero_whatsapp_cta_label',
					'type'         => 'text',
					'instructions' => 'Напис на кнопці WhatsApp (напр. «Написати у WhatsApp»).',
					'required'     => 0,
					'wrapper'      => array(
						'width' => '50',
					),
				),
				array(
					'key'          => 'field_amalfitana_home_hero_video_url',
					'label'        => 'URL відео Hero',
					'name'         => 'hero_background_video_url',
					'type'         => 'url',
					'instructions' => 'Пряме посилання на mp4 (наприклад, з CDN). Має пріоритет над завантаженим файлом.',
					'required'     => 0,
					'wrapper'      => array(
						'width' => '50',
					),
				),
				array(
					'key'           => 'field_amalfitana_home_hero_video',
					'label'         => 'Фонове відео Hero (mp4)',
					'name'          => 'hero_background_video',
					'type'          => 'file',
					'instructions'  => 'Завантажте mp4 для фону головного екрану. Якщо порожньо — залишиться поточне фонове зображення.',
					'required'      => 0,
					'return_format' => 'url',
					'library'       => 'all',
					'mime_types'    => 'mp4',
					'wrapper'       => array(
						'width' => '50',
					),
				),
				array(
					'key'          => 'field_amalfitana_home_about_title',
					'label'        => 'Заголовок секції «Про мене»',
					'name'         => 'about_section_title',
					'type'         => 'text',
					'instructions' => 'Напр.: «Про мене».',
					'required'     => 0,
					'wrapper'      => array(
						'width' => '100',
					),
				),
				array(
					'key'           => 'field_amalfitana_home_about_author_image',
					'label'         => 'Фото автора',
					'name'          => 'about_author_image',
					'type'          => 'image',
					'instructions'  => 'Статичне фото автора. Використовується, якщо відео не завантажено.',
					'required'      => 0,
					'return_format' => 'url',
					'preview_size'  => 'medium',
					'library'       => 'all',
					'mime_types'    => 'jpg,jpeg,png,webp',
					'wrapper'       => array(
						'width' => '50',
					),
				),
				array(
					'key'           => 'field_amalfitana_home_about_video',
					'label'         => 'Відео автора (mp4)',
					'name'          => 'about_video',
					'type'          => 'file',
					'instructions'  => 'Автовідтворюване відео в блоці «Про мене». Має пріоритет над фото автора.',
					'required'      => 0,
					'return_format' => 'url',
					'library'       => 'all',
					'mime_types'    => 'video/mp4,mp4',
					'wrapper'       => array(
						'width' => '50',
					),
				),
				array(
					'key'          => 'field_amalfitana_home_about_main_text',
					'label'        => 'Основний текст «Про мене»',
					'name'         => 'about_main_text',
					'type'         => 'wysiwyg',
					'instructions' => 'Текст біографії під картками переваг.',
					'required'     => 0,
					'tabs'         => 'all',
					'toolbar'      => 'basic',
					'media_upload' => 0,
					'delay'        => 0,
					'wrapper'      => array(
						'width' => '100',
					),
				),
				array(
					'key'          => 'field_amalfitana_home_about_card_1_title',
					'label'        => 'Про мене: Картка 1 — Заголовок',
					'name'         => 'about_card_1_title',
					'type'         => 'text',
					'instructions' => 'Заголовок першої картки переваг (напр. «В Італії 5+ років»).',
					'required'     => 0,
					'wrapper'      => array(
						'width' => '50',
					),
				),
				array(
					'key'          => 'field_amalfitana_home_about_card_1_text',
					'label'        => 'Про мене: Картка 1 — Опис',
					'name'         => 'about_card_1_text',
					'type'         => 'textarea',
					'instructions' => 'Короткий опис першої картки переваг.',
					'required'     => 0,
					'rows'         => 3,
					'new_lines'    => '',
					'wrapper'      => array(
						'width' => '50',
					),
				),
				array(
					'key'          => 'field_amalfitana_home_about_card_2_title',
					'label'        => 'Про мене: Картка 2 — Заголовок',
					'name'         => 'about_card_2_title',
					'type'         => 'text',
					'instructions' => 'Заголовок другої картки переваг (напр. «Знаю скриті місця»).',
					'required'     => 0,
					'wrapper'      => array(
						'width' => '50',
					),
				),
				array(
					'key'          => 'field_amalfitana_home_about_card_2_text',
					'label'        => 'Про мене: Картка 2 — Опис',
					'name'         => 'about_card_2_text',
					'type'         => 'textarea',
					'instructions' => 'Короткий опис другої картки переваг.',
					'required'     => 0,
					'rows'         => 3,
					'new_lines'    => '',
					'wrapper'      => array(
						'width' => '50',
					),
				),
				array(
					'key'          => 'field_amalfitana_home_about_button_label',
					'label'        => 'Про мене: Текст кнопки',
					'name'         => 'about_button_label',
					'type'         => 'text',
					'instructions' => 'Напис на кнопці під біографією (напр. «Більше про мене»).',
					'required'     => 0,
					'wrapper'      => array(
						'width' => '100',
					),
				),
				array(
					'key'          => 'field_amalfitana_home_contact_instagram',
					'label'        => 'Instagram',
					'name'         => 'contact_instagram',
					'type'         => 'url',
					'instructions' => 'Повне посилання на профіль Instagram.',
					'required'     => 0,
					'wrapper'      => array(
						'width' => '20',
					),
				),
				array(
					'key'          => 'field_amalfitana_home_contact_telegram',
					'label'        => 'Telegram',
					'name'         => 'contact_telegram',
					'type'         => 'url',
					'instructions' => 'Повне посилання на Telegram (напр. https://t.me/username).',
					'required'     => 0,
					'wrapper'      => array(
						'width' => '20',
					),
				),
				array(
					'key'          => 'field_amalfitana_home_contact_whatsapp',
					'label'        => 'WhatsApp',
					'name'         => 'contact_whatsapp',
					'type'         => 'text',
					'instructions' => 'Номер телефону (+393279140443) або повне посилання wa.me.',
					'required'     => 0,
					'wrapper'      => array(
						'width' => '20',
					),
				),
				array(
					'key'          => 'field_amalfitana_home_contact_phone',
					'label'        => 'Номер телефону',
					'name'         => 'contact_phone',
					'type'         => 'text',
					'instructions' => 'Номер для tel:-посилання на сайті (напр. +39 327 914 0443).',
					'required'     => 0,
					'wrapper'      => array(
						'width' => '20',
					),
				),
				array(
					'key'          => 'field_amalfitana_home_contact_email',
					'label'        => 'Email',
					'name'         => 'contact_email',
					'type'         => 'email',
					'instructions' => 'Контактна адреса для mailto-посилань на сайті.',
					'required'     => 0,
					'wrapper'      => array(
						'width' => '20',
					),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'page_type',
						'operator' => '==',
						'value'    => 'front_page',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		)
	);
}
add_action( 'acf/include_fields', 'amalfitana_register_acf_field_groups' );
