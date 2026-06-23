<?php
/**
 * Experience CPT template rendering helpers.
 *
 * @package Amalfitana_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fetch published experience posts.
 *
 * @param int $limit Maximum posts to fetch (-1 for all).
 * @return WP_Query
 */
function amalfitana_get_published_experiences_query( $limit = -1 ) {
	return new WP_Query(
		array(
			'post_type'      => 'experience',
			'post_status'    => 'publish',
			'posts_per_page' => (int) $limit,
			'orderby'        => 'date',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);
}

/**
 * Fetch (and statically cache) the experiences shown on the front page.
 *
 * Both the desktop slider and the mobile swiper reuse this single query so the
 * front page never runs the experience query twice.
 *
 * @return WP_Post[]
 */
function amalfitana_get_home_experience_posts() {
	static $posts = null;

	if ( null !== $posts ) {
		return $posts;
	}

	$query = amalfitana_get_published_experiences_query( 10 );
	$posts = $query->have_posts() ? $query->posts : array();

	return $posts;
}

/**
 * Read an experience ACF/text field for a post.
 *
 * @param int    $post_id Post ID.
 * @param string $field   Field name.
 * @return string
 */
function amalfitana_get_experience_field( $post_id, $field ) {
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $field, $post_id );
		return is_string( $value ) ? trim( $value ) : '';
	}

	return '';
}

/**
 * Return calendar badge SVG for tour cards.
 *
 * @param string $suffix Unique suffix for SVG mask IDs.
 * @return string
 */
function amalfitana_get_tour_card_calendar_svg( $suffix = '' ) {
	$mask_id = 'mask0_257_201';
	if ( '' !== $suffix ) {
		$mask_id .= '_' . preg_replace( '/[^a-z0-9_]/i', '_', $suffix );
	}

	return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><mask id="' . esc_attr( $mask_id ) . '" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24"><rect width="24" height="24" fill="#D9D9D9"/></mask><g mask="url(#' . esc_attr( $mask_id ) . ')"><path d="M5.30775 21.5C4.80258 21.5 4.375 21.325 4.025 20.975C3.675 20.625 3.5 20.1974 3.5 19.6923V6.30777C3.5 5.8026 3.675 5.37502 4.025 5.02502C4.375 4.67502 4.80258 4.50002 5.30775 4.50002H6.69225V2.38477H8.23075V4.50002H15.8077V2.38477H17.3077V4.50002H18.6923C19.1974 4.50002 19.625 4.67502 19.975 5.02502C20.325 5.37502 20.5 5.8026 20.5 6.30777V19.6923C20.5 20.1974 20.325 20.625 19.975 20.975C19.625 21.325 19.1974 21.5 18.6923 21.5H5.30775ZM5.30775 20H18.6923C18.7692 20 18.8398 19.9679 18.9038 19.9038C18.9679 19.8398 19 19.7693 19 19.6923V10.3078H5V19.6923C5 19.7693 5.03208 19.8398 5.09625 19.9038C5.16025 19.9679 5.23075 20 5.30775 20ZM5 8.80777H19V6.30777C19 6.23077 18.9679 6.16026 18.9038 6.09626C18.8398 6.0321 18.7692 6.00002 18.6923 6.00002H5.30775C5.23075 6.00002 5.16025 6.0321 5.09625 6.09626C5.03208 6.16026 5 6.23077 5 6.30777V8.80777ZM12 14.077C11.7552 14.077 11.5465 13.9908 11.374 13.8183C11.2017 13.6459 11.1155 13.4373 11.1155 13.1923C11.1155 12.9474 11.2017 12.7388 11.374 12.5663C11.5465 12.3939 11.7552 12.3078 12 12.3078C12.2448 12.3078 12.4535 12.3939 12.626 12.5663C12.7983 12.7388 12.8845 12.9474 12.8845 13.1923C12.8845 13.4373 12.7983 13.6459 12.626 13.8183C12.4535 13.9908 12.2448 14.077 12 14.077ZM7.374 13.8183C7.20167 13.6459 7.1155 13.4373 7.1155 13.1923C7.1155 12.9474 7.20167 12.7388 7.374 12.5663C7.5465 12.3939 7.75517 12.3078 8 12.3078C8.24483 12.3078 8.4535 12.3939 8.626 12.5663C8.79833 12.7388 8.8845 12.9474 8.8845 13.1923C8.8845 13.4373 8.79833 13.6459 8.626 13.8183C8.4535 13.9908 8.24483 14.077 8 14.077C7.75517 14.077 7.5465 13.9908 7.374 13.8183ZM16 14.077C15.7552 14.077 15.5465 13.9908 15.374 13.8183C15.2017 13.6459 15.1155 13.4373 15.1155 13.1923C15.1155 12.9474 15.2017 12.7388 15.374 12.5663C15.5465 12.3939 15.7552 12.3078 16 12.3078C16.2448 12.3078 16.4535 12.3939 16.626 12.5663C16.7983 12.7388 16.8845 12.9474 16.8845 13.1923C16.8845 13.4373 16.7983 13.6459 16.626 13.8183C16.4535 13.9908 16.2448 14.077 16 14.077ZM12 18C11.7552 18 11.5465 17.9138 11.374 17.7413C11.2017 17.5689 11.1155 17.3603 11.1155 17.1155C11.1155 16.8705 11.2017 16.6618 11.374 16.4895C11.5465 16.317 11.7552 16.2308 12 16.2308C12.2448 16.2308 12.4535 16.317 12.626 16.4895C12.7983 16.6618 12.8845 16.8705 12.8845 17.1155C12.8845 17.3603 12.7983 17.5689 12.626 17.7413C12.4535 17.9138 12.2448 18 12 18ZM7.374 17.7413C7.20167 17.5689 7.1155 17.3603 7.1155 17.1155C7.1155 16.8705 7.20167 16.6618 7.374 16.4895C7.5465 16.317 7.75517 16.2308 8 16.2308C8.24483 16.2308 8.4535 16.317 8.626 16.4895C8.79833 16.6618 8.8845 16.8705 8.8845 17.1155C8.8845 17.3603 8.79833 17.5689 8.626 17.7413C8.4535 17.9138 8.24483 18 8 18C7.75517 18 7.5465 17.9138 7.374 17.7413ZM16 18C15.7552 18 15.5465 17.9138 15.374 17.7413C15.2017 17.5689 15.1155 17.3603 15.1155 17.1155C15.1155 16.8705 15.2017 16.6618 15.374 16.4895C15.5465 16.317 15.7552 16.2308 16 16.2308C16.2448 16.2308 16.4535 16.317 16.626 16.4895C16.7983 16.6618 16.8845 16.8705 16.8845 17.1155C16.8845 17.3603 16.7983 17.5689 16.626 17.7413C16.4535 17.9138 16.2448 18 16 18Z" fill="#E87F24"/></g></svg>';
}

/**
 * Return avatar badge SVG for tour cards.
 *
 * @param string $suffix Unique suffix for SVG mask IDs.
 * @return string
 */
function amalfitana_get_tour_card_avatar_svg( $suffix = '' ) {
	$mask_id = 'mask0_257_206';
	if ( '' !== $suffix ) {
		$mask_id .= '_' . preg_replace( '/[^a-z0-9_]/i', '_', $suffix );
	}

	return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><mask id="' . esc_attr( $mask_id ) . '" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24"><rect width="24" height="24" fill="#D9D9D9"/></mask><g mask="url(#' . esc_attr( $mask_id ) . ')"><path d="M6.023 17.2923C6.873 16.6616 7.799 16.1635 8.801 15.798C9.80283 15.4327 10.8692 15.25 12 15.25C13.1308 15.25 14.1972 15.4327 15.199 15.798C16.201 16.1635 17.127 16.6616 17.977 17.2923C18.5987 16.6089 19.0912 15.8179 19.4548 14.9192C19.8183 14.0206 20 13.0475 20 12C20 9.78333 19.2208 7.89583 17.6625 6.3375C16.1042 4.77917 14.2167 4 12 4C9.78333 4 7.89583 4.77917 6.3375 6.3375C4.77917 7.89583 4 9.78333 4 12C4 13.0475 4.18175 14.0206 4.54525 14.9192C4.90875 15.8179 5.40133 16.6089 6.023 17.2923ZM9.6905 11.8095C9.0635 11.1827 8.75 10.4128 8.75 9.5C8.75 8.58717 9.0635 7.81733 9.6905 7.1905C10.3173 6.5635 11.0872 6.25 12 6.25C12.9128 6.25 13.6827 6.5635 14.3095 7.1905C14.9365 7.81733 15.25 8.58717 15.25 9.5C15.25 10.4128 14.9365 11.1827 14.3095 11.8095C13.6827 12.4365 12.9128 12.75 12 12.75C11.0872 12.75 10.3173 12.4365 9.6905 11.8095ZM12 21.5C10.6808 21.5 9.44333 21.2519 8.2875 20.7558C7.13167 20.2596 6.12625 19.5839 5.27125 18.7288C4.41608 17.8738 3.74042 16.8683 3.24425 15.7125C2.74808 14.5567 2.5 13.3192 2.5 12C2.5 10.6808 2.74808 9.44333 3.24425 8.2875C3.74042 7.13167 4.41608 6.12625 5.27125 5.27125C6.12625 4.41608 7.13167 3.74042 8.2875 3.24425C9.44333 2.74808 10.6808 2.5 12 2.5C13.3192 2.5 14.5567 2.74808 15.7125 3.24425C16.8683 3.74042 17.8738 4.41608 18.7288 5.27125C19.5839 6.12625 20.2596 7.13167 20.7558 8.2875C21.2519 9.44333 21.5 10.6808 21.5 12C21.5 13.3192 21.2519 14.5567 20.7558 15.7125C20.2596 16.8683 19.5839 17.8738 18.7288 18.7288C17.8738 19.5839 16.8683 20.2596 15.7125 20.7558C14.5567 21.2519 13.3192 21.5 12 21.5ZM14.6105 19.5645C15.4483 19.274 16.1923 18.8679 16.8422 18.3462C16.1923 17.8436 15.458 17.4519 14.6395 17.1712C13.8208 16.8904 12.941 16.75 12 16.75C11.059 16.75 10.1776 16.8888 9.35575 17.1663C8.53392 17.4439 7.80125 17.8372 7.15775 18.3462C7.80775 18.8679 8.55167 19.274 9.3895 19.5645C10.2273 19.8548 11.0975 20 12 20C12.9025 20 13.7727 19.8548 14.6105 19.5645ZM13.248 10.748C13.5827 10.4135 13.75 9.9975 13.75 9.5C13.75 9.0025 13.5827 8.5865 13.248 8.252C12.9135 7.91733 12.4975 7.75 12 7.75C11.5025 7.75 11.0865 7.91733 10.752 8.252C10.4173 8.5865 10.25 9.0025 10.25 9.5C10.25 9.9975 10.4173 10.4135 10.752 10.748C11.0865 11.0827 11.5025 11.25 12 11.25C12.4975 11.25 12.9135 11.0827 13.248 10.748Z" fill="#E87F24"/></g></svg>';
}

/**
 * Return five-star markup used by tour cards.
 *
 * @param string $suffix Unique suffix for SVG mask IDs.
 * @return string
 */
function amalfitana_get_tour_card_stars_markup( $suffix = '' ) {
	$star_path = 'M5.08337 4.26671L6.95004 1.85004C7.08337 1.67226 7.24171 1.54171 7.42504 1.45837C7.60837 1.37504 7.80004 1.33337 8.00004 1.33337C8.20004 1.33337 8.39171 1.37504 8.57504 1.45837C8.75837 1.54171 8.91671 1.67226 9.05004 1.85004L10.9167 4.26671L13.75 5.21671C14.0389 5.3056 14.2667 5.46949 14.4334 5.70837C14.6 5.94726 14.6834 6.21115 14.6834 6.50004C14.6834 6.63337 14.6639 6.76671 14.625 6.90004C14.5862 7.03337 14.5223 7.16115 14.4334 7.28337L12.6 9.88337L12.6667 12.6167C12.6778 13.0056 12.55 13.3334 12.2834 13.6C12.0167 13.8667 11.7056 14 11.35 14C11.3278 14 11.2056 13.9834 10.9834 13.95L8.00004 13.1167L5.01671 13.95C4.96115 13.9723 4.90004 13.9862 4.83337 13.9917C4.76671 13.9973 4.7056 14 4.65004 14C4.29448 14 3.98337 13.8667 3.71671 13.6C3.45004 13.3334 3.32226 13.0056 3.33337 12.6167L3.40004 9.86671L1.58337 7.28337C1.49449 7.16115 1.4306 7.03337 1.39171 6.90004C1.35282 6.76671 1.33337 6.63337 1.33337 6.50004C1.33337 6.22226 1.41393 5.96393 1.57504 5.72504C1.73615 5.48615 1.96115 5.31671 2.25004 5.21671L5.08337 4.26671Z';
	$mask_id   = 'mask0_23_211';
	if ( '' !== $suffix ) {
		$mask_id .= '_' . preg_replace( '/[^a-z0-9_]/i', '_', $suffix );
	}
	$star_svg  = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none"><mask id="' . esc_attr( $mask_id ) . '" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="16" height="16"><rect width="16" height="16" fill="#D9D9D9"/></mask><g mask="url(#' . esc_attr( $mask_id ) . ')"><path d="' . $star_path . '" fill="#E87F24"/></g></svg>';

	return '<span class="tour-card__stars" aria-label="5 out of 5 stars">' . str_repeat( $star_svg, 5 ) . '</span>';
}

/**
 * Format tour card price markup.
 *
 * @param int $post_id Experience post ID.
 * @return string
 */
function amalfitana_format_tour_card_price_markup( $post_id ) {
	$post_id   = (int) $post_id;
	$raw_price = amalfitana_get_experience_field( $post_id, 'experience_price' );
	$raw_price = trim( (string) $raw_price );

	if ( '' === $raw_price ) {
		$raw_price = '550 €';
	}

	if ( preg_match( '/\d/', $raw_price ) ) {
		$raw_price = trim( preg_replace( '/^від\s+/ui', '', $raw_price ) );

		ob_start();
		?>
		<div class="tour-card__price-wrapper" style="display: flex; align-items: baseline; gap: 6px;">
			<span class="tour-card__price-label">ВІД</span>
			<span class="tour-card__price-value"><?php echo esc_html( $raw_price ); ?></span>
		</div>
		<?php
		return ob_get_clean();
	}

	ob_start();
		?>
		<div class="tour-card__price-value" style="display: flex; align-items: center; gap: 6px; font-size: 1.1rem; text-transform: none; line-height: 1.2;">
			За запитом
			<span title="<?php echo esc_attr( $raw_price ); ?>" style="cursor: help; display: inline-flex; pointer-events: auto;">
				<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#E87F24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<circle cx="12" cy="12" r="10"></circle>
					<line x1="12" y1="16" x2="12" y2="12"></line>
					<line x1="12" y1="8" x2="12.01" y2="8"></line>
				</svg>
			</span>
		</div>
		<?php
		return ob_get_clean();
}

/**
 * Render one experience tour card.
 *
 * @param int    $post_id Post ID.
 * @param string $suffix  Unique suffix for inline SVG IDs.
 * @return string
 */
function amalfitana_render_experience_tour_card( $post_id, $suffix = '' ) {
	$post_id   = (int) $post_id;
	$title     = get_the_title( $post_id );
	$permalink = get_permalink( $post_id );
	$image_url = get_the_post_thumbnail_url( $post_id, 'full' );

	if ( ! $image_url ) {
		$image_url = amalfitana_get_media_url_by_filename( 'swiper-img1.png', 'full' );
	}

	$duration      = amalfitana_get_experience_field( $post_id, 'experience_duration' );
	$hero_subtitle = amalfitana_get_experience_field( $post_id, 'experience_hero_subtitle' );
	$duration      = '' !== $duration ? $duration : '1 - 3 дні';
	$price_markup  = amalfitana_format_tour_card_price_markup( $post_id );

	ob_start();
	?>
		<article class="tour-card">
					<div class="tour-card__image-wrap">
						<img class="tour-card__image" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>">
					</div>
		
					<div class="tour-card__content">
						<div class="tour-card__meta">
							<div class="tour-card__meta-pills">
								<span class="tour-card__badge tour-card__metric" title="<?php echo esc_attr( $duration ); ?>">
									<?php echo amalfitana_get_tour_card_calendar_svg( $suffix ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG markup. ?>
									<span class="tour-card__metric-text"><?php echo esc_html( $duration ); ?></span>
								</span>
								<span class="tour-card__badge tour-card__metric" title="<?php echo esc_attr__( 'Індивідуальний', 'amalfitana-theme' ); ?>">
									<?php echo amalfitana_get_tour_card_avatar_svg( $suffix ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG markup. ?>
									<span class="tour-card__metric-text"><?php esc_html_e( 'Індивідуальний', 'amalfitana-theme' ); ?></span>
								</span>
							</div>
						</div>

						<div class="tour-card__rating">
							<?php echo amalfitana_get_tour_card_stars_markup( $suffix ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG markup. ?>
						</div>
		
					<h3 class="tour-card__title"><?php echo esc_html( $title ); ?></h3>
		
					<?php if ( '' !== $hero_subtitle ) : ?>
					<p class="tour-card__desc"><?php echo esc_html( $hero_subtitle ); ?></p>
					<?php endif; ?>
		
					<div class="tour-card__footer">
							<div class="tour-card__price">
								<?php echo $price_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in formatter. ?>
							</div>
							<a class="btn btn--outline tour-card__details-link" href="<?php echo esc_url( $permalink ); ?>">
								<span>Дивитися деталі</span>
								<svg class="btn__icon" xmlns="http://www.w3.org/2000/svg" width="13" height="10" viewBox="0 0 13 10" fill="none" aria-hidden="true">
									<path d="M7.78854 9.42292L6.91021 8.51917L10.0929 5.33646H0V4.08646H10.0929L6.91021 0.90375L7.78854 0L12.5 4.71146L7.78854 9.42292Z" />
								</svg>
							</a>
						</div>
					</div>
				</article>
	<?php
	return ob_get_clean();
}

/**
 * Render desktop slider cards for the front page.
 *
 * @return string
 */
function amalfitana_render_home_tours_slider_cards() {
	$output = '';

	foreach ( amalfitana_get_home_experience_posts() as $experience ) {
		$output .= amalfitana_render_experience_tour_card( $experience->ID, 'desk_' . $experience->ID );
	}

	return $output;
}

/**
 * Render mobile swiper slides for the front page.
 *
 * @return string
 */
function amalfitana_render_home_tours_mobile_slides() {
	$output = '';

	foreach ( amalfitana_get_home_experience_posts() as $experience ) {
		$output .= '<div class="swiper-slide">' . amalfitana_render_experience_tour_card( $experience->ID, 'mob_' . $experience->ID ) . '</div>';
	}

	return $output;
}

/**
 * Render tours grid items for the tours archive page.
 *
 * @return string
 */
function amalfitana_render_tours_grid_items() {
	$query  = amalfitana_get_published_experiences_query();
	$output = '';

	if ( $query->have_posts() ) {
		$index = 0;

		while ( $query->have_posts() ) {
			$query->the_post();

			$delay_class = '';
			if ( 1 === $index % 3 ) {
				$delay_class = ' animate-on-scroll--delay-200';
			} elseif ( 2 === $index % 3 ) {
				$delay_class = ' animate-on-scroll--delay-400';
			}

			$output .= '<li class="tours-grid__item animate-on-scroll' . esc_attr( $delay_class ) . '">';
			$output .= amalfitana_render_experience_tour_card( get_the_ID(), 'grid_' . get_the_ID() );
			$output .= '</li>';

			$index++;
		}

		wp_reset_postdata();
	}

	return $output;
}

/**
 * Marker replaced on the front end with ACF-driven included/not-included section.
 */
function amalfitana_get_experience_acf_marker() {
	return '<!-- EXPERIENCE_ACF_SECTIONS -->';
}

/**
 * Return the checkmark SVG used in tour detail lists.
 *
 * @return string
 */
function amalfitana_get_tour_detail_check_icon_svg() {
	return '<svg class="tour-detail-list__icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M14.875 5.125C14.2917 4.54167 14 3.83333 14 3C14 2.16667 14.2917 1.45833 14.875 0.875C15.4583 0.291667 16.1667 0 17 0C17.8333 0 18.5417 0.291667 19.125 0.875C19.7083 1.45833 20 2.16667 20 3C20 3.83333 19.7083 4.54167 19.125 5.125C18.5417 5.70833 17.8333 6 17 6C16.1667 6 15.4583 5.70833 14.875 5.125ZM6.1 19.2125C4.88333 18.6875 3.825 17.975 2.925 17.075C2.025 16.175 1.3125 15.1167 0.7875 13.9C0.2625 12.6833 0 11.3833 0 10C0 8.61667 0.2625 7.31667 0.7875 6.1C1.3125 4.88333 2.025 3.825 2.925 2.925C3.825 2.025 4.88333 1.3125 6.1 0.7875C7.31667 0.2625 8.61667 0 10 0C10.4667 0 10.9292 0.0333333 11.3875 0.1C11.8458 0.166667 12.3 0.266667 12.75 0.4C12.5667 0.683333 12.4167 0.9875 12.3 1.3125C12.1833 1.6375 12.1 1.96667 12.05 2.3C11.7167 2.2 11.3792 2.125 11.0375 2.075C10.6958 2.025 10.35 2 10 2C7.76667 2 5.875 2.775 4.325 4.325C2.775 5.875 2 7.76667 2 10C2 12.2333 2.775 14.125 4.325 15.675C5.875 17.225 7.76667 18 10 18C12.2333 18 14.125 17.225 15.675 15.675C17.225 14.125 18 12.2333 18 10C18 9.65 17.975 9.30417 17.925 8.9625C17.875 8.62083 17.8 8.28333 17.7 7.95C18.0333 7.9 18.3625 7.81667 18.6875 7.7C19.0125 7.58333 19.3167 7.43333 19.6 7.25C19.7333 7.7 19.8333 8.15417 19.9 8.6125C19.9667 9.07083 20 9.53333 20 10C20 11.3833 19.7375 12.6833 19.2125 13.9C18.6875 15.1167 17.975 16.175 17.075 17.075C16.175 17.975 15.1167 18.6875 13.9 19.2125C12.6833 19.7375 11.3833 20 10 20C8.61667 20 7.31667 19.7375 6.1 19.2125ZM8.575 14.6L15.4 7.775C15.0667 7.65833 14.7542 7.5125 14.4625 7.3375C14.1708 7.1625 13.8917 6.95833 13.625 6.725L8.6 11.75L5.75 8.95L4.35 10.35L8.575 14.6Z" fill="#E87F24"/></svg>';
}

/**
 * Parse newline or legacy list markup into plain-text lines.
 *
 * @param string $text Raw field value.
 * @return string[]
 */
function amalfitana_parse_experience_list_lines( $text ) {
	$text = trim( (string) $text );

	if ( '' === $text ) {
		return array();
	}

	if ( false !== strpos( $text, '<li' ) ) {
		preg_match_all( '#<li[^>]*>(.*?)</li>#isu', $text, $matches );
		$items = array();

		foreach ( $matches[1] as $item ) {
			$line = trim( wp_strip_all_tags( (string) $item ) );
			if ( '' !== $line ) {
				$items[] = $line;
			}
		}

		if ( ! empty( $items ) ) {
			return $items;
		}
	}

	$lines = preg_split( '/\r\n|\r|\n/', $text );
	$items = array();

	foreach ( $lines as $line ) {
		$line = trim( wp_strip_all_tags( (string) $line ) );
		if ( '' !== $line ) {
			$items[] = $line;
		}
	}

	return $items;
}

/**
 * Render a tour detail list with checkmark icons.
 *
 * @param string[] $items List item labels.
 * @return string
 */
function amalfitana_render_tour_detail_icon_list( $items ) {
	if ( empty( $items ) ) {
		return '';
	}

	$icon = amalfitana_get_tour_detail_check_icon_svg();
	$lis  = '';

	foreach ( $items as $item ) {
		$lis .= '<li class="tour-detail-list__item">' . $icon . '<span class="tour-detail-list__text">' . esc_html( $item ) . '</span></li>';
	}

	return '<ul class="tour-detail-list animate-on-scroll">' . $lis . '</ul>';
}

/**
 * Render the included-items list with orange dash bullets.
 *
 * @param string[] $items List item labels.
 * @return string
 */
function amalfitana_render_tour_detail_included_list( $items ) {
	if ( empty( $items ) ) {
		return '';
	}

	$lis = '';

	foreach ( $items as $item ) {
		$lis .= '<li class="tour-detail-list__item"><span class="tour-detail-list__dash" aria-hidden="true"></span><span class="tour-detail-list__text">' . esc_html( $item ) . '</span></li>';
	}

	return '<ul class="tour-detail-list tour-detail-list--included animate-on-scroll">' . $lis . '</ul>';
}

/**
 * Render the ACF-driven included / not-included section.
 *
 * @param int $post_id Experience post ID.
 * @return string
 */
function amalfitana_render_experience_included_section( $post_id ) {
	$included = amalfitana_parse_experience_list_lines( amalfitana_get_experience_field( $post_id, 'experience_included' ) );
	$not_inc  = trim( wp_strip_all_tags( amalfitana_get_experience_field( $post_id, 'experience_not_included' ) ) );

	if ( empty( $included ) && '' === $not_inc ) {
		return '';
	}

	ob_start();
	?>
	<section class="tour-detail-content__section">
		<?php if ( ! empty( $included ) ) : ?>
			<h2 class="tour-detail-content__heading animate-on-scroll">У вартість входить</h2>
			<?php echo amalfitana_render_tour_detail_included_list( $included ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in renderer. ?>
		<?php endif; ?>
		<?php if ( '' !== $not_inc ) : ?>
			<p class="tour-detail-content__note animate-on-scroll"><?php echo esc_html( 'Не включено: ' . $not_inc ); ?></p>
		<?php endif; ?>
	</section>
	<?php
	return ob_get_clean();
}

/**
 * Resolve the explanatory hero line from ACF.
 *
 * @param int    $post_id Post ID.
 * @param string $title   Post title (unused; kept for backward compatibility).
 * @return string
 */
function amalfitana_get_experience_hero_subtitle( $post_id, $title ) {
	unset( $title );

	return amalfitana_get_experience_field( $post_id, 'experience_hero_subtitle' );
}

/**
 * Inject ACF sections into experience post content.
 *
 * @param string $content Post content HTML.
 * @return string
 */
function amalfitana_inject_experience_acf_sections( $content ) {
	if ( ! is_singular( 'experience' ) ) {
		return $content;
	}

	$post_id = get_queried_object_id();
	$marker  = amalfitana_get_experience_acf_marker();
	$section = amalfitana_render_experience_included_section( $post_id );

	if ( false !== strpos( $content, $marker ) ) {
		return str_replace( $marker, $section, $content );
	}

	if ( '' !== $section ) {
		return $content . $section;
	}

	return $content;
}
add_filter( 'the_content', 'amalfitana_inject_experience_acf_sections', 12 );

/**
 * Normalize Gutenberg wrappers inside the experience prose stream.
 *
 * @param string $content Post content HTML.
 * @return string
 */
function amalfitana_normalize_experience_content_wrappers( $content ) {
	if ( ! is_singular( 'experience' ) ) {
		return $content;
	}

	$content = preg_replace(
		'#<div class="wp-block-html">\s*(<!-- EXPERIENCE_ACF_SECTIONS -->)\s*</div>#',
		'$1',
		$content
	);

	return $content;
}
add_filter( 'the_content', 'amalfitana_normalize_experience_content_wrappers', 11 );

/**
 * Render the format metric row with optional tooltip.
 *
 * @param int $post_id Experience post ID.
 * @return string
 */
function amalfitana_render_experience_format_metric( $post_id ) {
	$format_short = amalfitana_get_experience_field( $post_id, 'experience_format_short' );
	$tooltip      = amalfitana_get_experience_field( $post_id, 'experience_what_to_take_tooltip' );

	if ( '' === $format_short ) {
		$format_short = 'Піша екскурсія';
	}

	$has_tooltip = '' !== $tooltip;

	ob_start();
	?>
	<div class="tour-detail-content__metric-value-row">
		<p class="tour-detail-content__metric-value"><?php echo esc_html( $format_short ); ?></p>
		<?php if ( $has_tooltip ) : ?>
			<span class="tour-detail-content__metric-tooltip">
				<button type="button" class="tour-detail-content__metric-tooltip-trigger" aria-label="Що взяти з собою">
					<svg class="tour-detail-content__metric-info" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
						<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
					</svg>
				</button>
				<span class="tour-detail-content__metric-tooltip-bubble" role="tooltip">
					<?php echo esc_html( $tooltip ); ?>
				</span>
			</span>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Extract a short plain-text summary for the format metric.
 *
 * @param string $html WYSIWYG HTML.
 * @return string
 */
function amalfitana_get_experience_format_summary( $html ) {
	$text = trim( wp_strip_all_tags( (string) $html ) );

	if ( '' === $text ) {
		return 'Піша екскурсія';
	}

	$lines = preg_split( '/\r\n|\r|\n/', $text );
	$first = trim( (string) ( $lines[0] ?? $text ) );

	if ( strlen( $first ) > 80 ) {
		$first = wp_trim_words( $first, 8, '…' );
	}

	return $first;
}

/**
 * Build replacement map for single experience templates.
 *
 * @return array<string, string>
 */
function amalfitana_get_single_experience_replacements() {
	$post_id = get_queried_object_id();

	if ( ! $post_id || 'experience' !== get_post_type( $post_id ) ) {
		return array();
	}

	$hero_image = get_the_post_thumbnail_url( $post_id, 'full' );
	if ( ! $hero_image ) {
		$hero_image = amalfitana_get_media_url_by_filename( 'Tours-hero__img.png', 'full' );
	}

	$duration   = amalfitana_get_experience_field( $post_id, 'experience_duration' );
	$price      = amalfitana_get_experience_field( $post_id, 'experience_price' );
	$content    = apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) );
	$title      = get_the_title( $post_id );
	$subtitle   = amalfitana_get_experience_hero_subtitle( $post_id, $title );

	return array(
		'{{experience_hero_image_url}}'   => esc_url( $hero_image ),
		'{{experience_hero_subtitle}}'    => esc_html( $title ),
		'{{experience_hero_title}}'       => esc_html( $subtitle ),
		'{{experience_breadcrumb_title}}' => esc_html( $title ),
		'{{experience_duration}}'         => esc_html( '' !== $duration ? $duration : 'до 8 годин' ),
		'{{experience_price}}'            => esc_html( '' !== $price ? $price : 'від 550 €' ),
		'{{experience_format_metric}}'    => amalfitana_render_experience_format_metric( $post_id ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in renderer.
		'{{experience_content}}'          => $content, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Filtered content.
		'{{home_url}}'                    => esc_url( home_url( '/' ) ),
		'{{tours_url}}'                   => amalfitana_get_tours_page_url(),
	);
}

/**
 * Replace experience and front-page placeholders inside HTML blocks.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Block data.
 * @return string
 */
function amalfitana_render_experience_template_content( $block_content, $block ) {
	static $is_rendering = false;

	if ( empty( $block['blockName'] ) || 'core/html' !== $block['blockName'] ) {
		return $block_content;
	}

	// Re-entrancy guard: building the single-experience replacements runs
	// apply_filters( 'the_content', ... ), which triggers do_blocks() and would
	// re-enter this same render_block filter, causing infinite recursion and
	// memory exhaustion. Bail out on any nested invocation.
	if ( $is_rendering ) {
		return $block_content;
	}

	$replacements = array();

	if ( is_page( 'tours' ) ) {
		$replacements['{{tours_grid_items}}'] = amalfitana_render_tours_grid_items();
	}

	if ( is_singular( 'experience' ) ) {
		$is_rendering = true;
		$replacements = array_merge( $replacements, amalfitana_get_single_experience_replacements() );
		$is_rendering = false;
	}

	if ( empty( $replacements ) ) {
		return $block_content;
	}

	$has_placeholder = false;
	foreach ( array_keys( $replacements ) as $placeholder ) {
		if ( false !== strpos( $block_content, $placeholder ) ) {
			$has_placeholder = true;
			break;
		}
	}

	if ( ! $has_placeholder ) {
		return $block_content;
	}

	return str_replace(
		array_keys( $replacements ),
		array_values( $replacements ),
		$block_content
	);
}
add_filter( 'render_block', 'amalfitana_render_experience_template_content', 25, 2 );
