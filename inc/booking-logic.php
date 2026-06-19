<?php
/**
 * Booking Logic — WooCommerce cart integration for experience bookings.
 *
 * @package Amalfitana
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------------------------------------------------------------
 * 1. Helper — find the "Резерв туру" product ID (cached)
 * -------------------------------------------------------------- */

function amalfitana_get_booking_product_id() {
	return 137;
}

/* ---------------------------------------------------------------
 * 2. AJAX handler — add booking product to cart & redirect
 * -------------------------------------------------------------- */

function amalfitana_book_tour_handler() {
	check_ajax_referer( 'tour_booking_nonce', 'nonce' );

	$tour_id = isset( $_POST['tour_id'] ) ? absint( $_POST['tour_id'] ) : 0;
	$guests  = isset( $_POST['guests'] )  ? absint( $_POST['guests'] )  : 1;
	$date    = isset( $_POST['date'] )     ? sanitize_text_field( $_POST['date'] ) : '';

	if ( ! $tour_id || ! $date ) {
		wp_send_json_error( array( 'message' => 'Будь ласка, заповніть усі поля.' ) );
	}

	$product_id = amalfitana_get_booking_product_id();

	if ( ! $product_id ) {
		wp_send_json_error( array( 'message' => 'Товар бронювання не знайдено.' ) );
	}

	WC()->cart->empty_cart();

	$cart_item_data = array(
		'booking_tour_id' => $tour_id,
		'booking_guests'  => $guests,
		'booking_date'    => $date,
	);

	$added = WC()->cart->add_to_cart( $product_id, $guests, 0, array(), $cart_item_data );

	if ( ! $added ) {
		wp_send_json_error( array( 'message' => 'Не вдалося додати бронювання до кошика.' ) );
	}

	wp_send_json_success( array(
		'redirect_url' => wc_get_checkout_url(),
	) );
}
add_action( 'wp_ajax_amalfitana_book_tour',        'amalfitana_book_tour_handler' );
add_action( 'wp_ajax_nopriv_amalfitana_book_tour', 'amalfitana_book_tour_handler' );

/* ---------------------------------------------------------------
 * 3. Persist custom cart-item data through the session
 * -------------------------------------------------------------- */

function amalfitana_add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
	if ( isset( $_POST['booking_tour_id'] ) ) {
		$cart_item_data['booking_tour_id'] = absint( $_POST['booking_tour_id'] );
	}
	if ( isset( $_POST['booking_guests'] ) ) {
		$cart_item_data['booking_guests'] = absint( $_POST['booking_guests'] );
	}
	if ( isset( $_POST['booking_date'] ) ) {
		$cart_item_data['booking_date'] = sanitize_text_field( $_POST['booking_date'] );
	}
	return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'amalfitana_add_cart_item_data', 10, 3 );

/* ---------------------------------------------------------------
 * 4. Dynamic pricing — set price from ACF "experience_price"
 * -------------------------------------------------------------- */

function amalfitana_set_booking_cart_price( $cart ) {
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}

	if ( empty( $cart ) || did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
		return;
	}

	foreach ( $cart->get_cart() as $cart_item ) {
		if ( empty( $cart_item['booking_tour_id'] ) ) {
			continue;
		}

		$tour_id = (int) $cart_item['booking_tour_id'];
		$price   = 150; // fallback

		if ( function_exists( 'get_field' ) ) {
			$acf_price = get_field( 'experience_price', $tour_id );
			if ( $acf_price && is_numeric( $acf_price ) ) {
				$price = (float) $acf_price;
			}
		}

		$cart_item['data']->set_price( $price );
	}
}
add_action( 'woocommerce_before_calculate_totals', 'amalfitana_set_booking_cart_price', 20, 1 );

/* ---------------------------------------------------------------
 * 5. Display custom booking data in cart & checkout
 * -------------------------------------------------------------- */

function amalfitana_display_cart_item_data( $item_data, $cart_item ) {
	if ( ! empty( $cart_item['booking_date'] ) ) {
		$item_data[] = array(
			'key'   => 'Дата',
			'value' => esc_html( $cart_item['booking_date'] ),
		);
	}

	if ( ! empty( $cart_item['booking_guests'] ) ) {
		$item_data[] = array(
			'key'   => 'Кількість гостей',
			'value' => esc_html( $cart_item['booking_guests'] ),
		);
	}

	if ( ! empty( $cart_item['booking_tour_id'] ) ) {
		$item_data[] = array(
			'key'   => 'Тур',
			'value' => esc_html( get_the_title( (int) $cart_item['booking_tour_id'] ) ),
		);
	}

	return $item_data;
}
add_filter( 'woocommerce_get_item_data', 'amalfitana_display_cart_item_data', 10, 2 );

/* ---------------------------------------------------------------
 * 6. Save booking meta to the order line item
 * -------------------------------------------------------------- */

function amalfitana_save_order_item_meta( $item, $cart_item_key, $values, $order ) {
	if ( ! empty( $values['booking_date'] ) ) {
		$item->add_meta_data( '_booking_date', sanitize_text_field( $values['booking_date'] ) );
	}

	if ( ! empty( $values['booking_guests'] ) ) {
		$item->add_meta_data( '_guest_count', absint( $values['booking_guests'] ) );
	}

	if ( ! empty( $values['booking_tour_id'] ) ) {
		$item->add_meta_data( '_tour_id', absint( $values['booking_tour_id'] ) );
	}
}
add_action( 'woocommerce_checkout_create_order_line_item', 'amalfitana_save_order_item_meta', 10, 4 );

/* ---------------------------------------------------------------
 * 7. Streamline checkout — remove unnecessary billing fields
 * -------------------------------------------------------------- */

function amalfitana_simplify_checkout_fields( $fields ) {
	$remove = array(
		'billing_company',
		'billing_country',
		'billing_address_1',
		'billing_address_2',
		'billing_city',
		'billing_state',
		'billing_postcode',
	);

	foreach ( $remove as $key ) {
		unset( $fields['billing'][ $key ] );
	}

	// Layout: two-column pairs & Localization
	if ( isset( $fields['billing']['billing_first_name'] ) ) {
		$fields['billing']['billing_first_name']['class'] = array( 'form-row-first' );
		$fields['billing']['billing_first_name']['label'] = "Ім'я";
	}
	if ( isset( $fields['billing']['billing_last_name'] ) ) {
		$fields['billing']['billing_last_name']['class'] = array( 'form-row-last' );
		$fields['billing']['billing_last_name']['label'] = "Прізвище";
	}
	if ( isset( $fields['billing']['billing_email'] ) ) {
		$fields['billing']['billing_email']['class'] = array( 'form-row-first' );
	}
	if ( isset( $fields['billing']['billing_phone'] ) ) {
		$fields['billing']['billing_phone']['class']    = array( 'form-row-last' );
		$fields['billing']['billing_phone']['required'] = true;
		$fields['billing']['billing_phone']['label']    = "Телефон";
	}
	
	// Localize Order Comments
	if ( isset( $fields['order']['order_comments'] ) ) {
		$fields['order']['order_comments']['label']       = "Додаткові побажання";
		$fields['order']['order_comments']['placeholder'] = "Ваші побажання щодо туру...";
	}

	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'amalfitana_simplify_checkout_fields' );

/* ---------------------------------------------------------------
 * 8. Translate Place Order button
 * -------------------------------------------------------------- */

add_filter( 'woocommerce_order_button_text', function() {
	return 'Підтвердити бронювання';
});

/* ---------------------------------------------------------------
 * 9. Display custom meta in order details and emails
 * -------------------------------------------------------------- */

function amalfitana_format_booking_meta( $formatted_meta, $item ) {
	$date    = $item->get_meta( '_booking_date' );
	$guests  = $item->get_meta( '_guest_count' );
	$tour_id = $item->get_meta( '_tour_id' );

	if ( $tour_id ) {
		$title = get_the_title( $tour_id );
		$formatted_meta['_tour_id'] = (object) array(
			'key'           => '_tour_id',
			'value'         => $title,
			'display_key'   => 'Обраний тур',
			'display_value' => $title,
		);
	}

	if ( $date ) {
		$formatted_meta['_booking_date'] = (object) array(
			'key'           => '_booking_date',
			'value'         => $date,
			'display_key'   => 'Дата туру',
			'display_value' => $date,
		);
	}

	if ( $guests ) {
		$formatted_meta['_guest_count'] = (object) array(
			'key'           => '_guest_count',
			'value'         => $guests,
			'display_key'   => 'Кількість гостей',
			'display_value' => $guests,
		);
	}

	return $formatted_meta;
}
add_filter( 'woocommerce_order_item_get_formatted_meta_data', 'amalfitana_format_booking_meta', 10, 2 );

/* ---------------------------------------------------------------
 * 10. Prevent direct access to technical booking product
 * -------------------------------------------------------------- */

function amalfitana_prevent_booking_product_access() {
	if ( is_singular( 'product' ) && get_the_ID() == amalfitana_get_booking_product_id() ) {
		wp_safe_redirect( home_url() );
		exit;
	}
}
add_action( 'template_redirect', 'amalfitana_prevent_booking_product_access' );
/* ---------------------------------------------------------------
 * 11. Programmatically override WooCommerce email branding
 * -------------------------------------------------------------- */

add_filter( 'option_woocommerce_email_base_color', function( $value ) {
	return '#e87f24';
} );

add_filter( 'option_woocommerce_email_background_color', function( $value ) {
	return '#f9fafa';
} );

add_filter( 'option_woocommerce_email_text_color', function( $value ) {
	return '#1a1a1a';
} );

add_filter( 'woocommerce_email_footer_text', function( $text ) {
	return '© 2026 tourism-guide. All rights reserved.';
} );
